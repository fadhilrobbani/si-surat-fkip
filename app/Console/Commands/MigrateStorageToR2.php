<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Storage;

class MigrateStorageToR2 extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'storage:migrate-to-r2
                            {--dry-run : Show what would be migrated without actually migrating}
                            {--directory= : Only migrate a specific directory (e.g. ttd, stempel, lampiran)}
                            {--limit= : Limit the number of files to migrate in this run (e.g. --limit=50)}
                            {--max-mb= : Stop migration once migrated data reaches this size in MB (e.g. --max-mb=5000 for 5GB)}
                            {--delete-local : Delete the local file after successful upload to free up hosting disk space}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Migrate files from local storage to Cloudflare R2 with partial/batch and quota limits';

    /**
     * Execute the command.
     */
    public function handle()
    {
        $isDryRun = $this->option('dry-run');
        $specificDir = $this->option('directory');
        $limit = $this->option('limit') ? (int) $this->option('limit') : null;
        $maxMb = $this->option('max-mb') ? (float) $this->option('max-mb') : null;
        $deleteLocal = (bool) $this->option('delete-local');

        if ($isDryRun) {
            $this->info('🏃 DRY RUN MODE - No files will be migrated or deleted');
        }

        if ($limit) {
            $this->info("⏱️  Batch Limit: Maksimal {$limit} file per sesi");
        }

        if ($maxMb) {
            $this->info("📦 Size Limit: Maksimal {$maxMb} MB per sesi");
        }

        if ($deleteLocal && !$isDryRun) {
            $this->warn("🗑️  Delete Local: File lokal akan langsung dihapus setelah sukses masuk R2");
        }

        // Verify R2 connection first
        if (!$isDryRun) {
            try {
                Storage::disk('r2')->put('_migration_test.txt', 'test');
                Storage::disk('r2')->delete('_migration_test.txt');
                $this->info('✅ R2 connection successful');
            } catch (\Exception $e) {
                $this->error('❌ Cannot connect to R2: ' . $e->getMessage());
                $this->error('Please check your CLOUDFLARE_R2_* environment variables.');
                return 1;
            }
        }

        $directories = [];

        if ($specificDir) {
            $directories[] = $specificDir;
        } else {
            $directories = ['ttd', 'stempel', 'lampiran'];
        }

        $totalMigrated = 0;
        $totalBytesMigrated = 0;
        $totalSkipped = 0;
        $totalFailed = 0;
        $reachedLimit = false;

        foreach ($directories as $dir) {
            if ($reachedLimit) {
                break;
            }

            $this->info("\n📁 Processing directory: {$dir}");

            $files = [];

            // 1. Scan from public disk (storage/app/public/{dir})
            try {
                $publicFiles = Storage::disk('public')->allFiles($dir);
                if (!empty($publicFiles)) {
                    $files = array_merge($files, $publicFiles);
                }
            } catch (\Throwable $e) {}

            // 2. Scan from legacy local path (storage/app/{dir}) if exists
            $legacyLocalPath = storage_path('app/' . $dir);
            if (is_dir($legacyLocalPath)) {
                $legacyFiles = $this->getLocalFiles($legacyLocalPath, $dir);
                if (!empty($legacyFiles)) {
                    $files = array_merge($files, $legacyFiles);
                }
            }

            // Remove any duplicate filenames
            $files = array_values(array_unique($files));

            if (empty($files)) {
                $this->warn("  ⚠️  No files found in {$dir}");
                continue;
            }

            // Sort files by modified time: Oldest first (chronological order)
            usort($files, function ($a, $b) {
                $timeA = $this->getLocalFileMTime($a);
                $timeB = $this->getLocalFileMTime($b);
                return $timeA <=> $timeB;
            });

            $this->info("  Found " . count($files) . " files");
            $bar = $this->output->createProgressBar(count($files));
            $bar->start();

            foreach ($files as $file) {
                // Check limit counts
                if ($limit !== null && $totalMigrated >= $limit) {
                    $reachedLimit = true;
                    $this->newLine();
                    $this->warn("🛑 Batas limit file ({$limit} files) tercapai.");
                    break;
                }

                if ($maxMb !== null && ($totalBytesMigrated / (1024 * 1024)) >= $maxMb) {
                    $reachedLimit = true;
                    $this->newLine();
                    $this->warn("🛑 Batas kuota size ({$maxMb} MB) tercapai.");
                    break;
                }

                $bar->advance();

                // Check if file already exists in R2
                if (Storage::disk('r2')->exists($file)) {
                    $totalSkipped++;
                    // If file is already in R2 and deleteLocal requested, clean up local
                    if ($deleteLocal && !$isDryRun) {
                        $this->deleteLocalFile($file);
                    }
                    continue;
                }

                if ($isDryRun) {
                    $totalMigrated++;
                    $fileSize = $this->getLocalFileSize($file);
                    $totalBytesMigrated += $fileSize;
                    continue;
                }

                try {
                    // Get file contents from source
                    $contents = $this->getFileContents($file);

                    if ($contents === null || $contents === false) {
                        $this->newLine();
                        $this->warn("  ⚠️  Could not read: {$file}");
                        $totalFailed++;
                        continue;
                    }

                    // Upload to R2
                    Storage::disk('r2')->put($file, $contents);
                    $fileSize = strlen($contents);
                    $totalBytesMigrated += $fileSize;
                    $totalMigrated++;

                    // Delete local file if requested with strict integrity validation
                    if ($deleteLocal) {
                        // Integrity check: verify file exists on R2 and exact byte size matches
                        if (Storage::disk('r2')->exists($file) && Storage::disk('r2')->size($file) === $fileSize) {
                            $this->deleteLocalFile($file);
                        } else {
                            $this->newLine();
                            $this->error("  ⚠️  Verifikasi ukuran file {$file} gagal di R2. File lokal dipertahankan demi keamanan.");
                        }
                    }
                } catch (\Exception $e) {
                    $this->newLine();
                    $this->error("  ❌ Failed to migrate {$file}: " . $e->getMessage());
                    $totalFailed++;
                }
            }

            $bar->finish();
            $this->newLine();
        }

        $mbMigrated = round($totalBytesMigrated / (1024 * 1024), 2);

        $this->newLine();
        $this->info('📊 Migration Summary:');
        $this->table(
            ['Metric', 'Value'],
            [
                ['Files Migrated', $totalMigrated],
                ['Data Migrated', "{$mbMigrated} MB"],
                ['Skipped (already exists)', $totalSkipped],
                ['Failed', $totalFailed],
            ]
        );

        if ($isDryRun) {
            $this->info("\n💡 Run without --dry-run to actually migrate files.");
        }

        if ($reachedLimit) {
            $this->info("\n👉 Kamu bisa jalankan perintah ini lagi di batch berikutnya.");
        }

        if ($totalFailed > 0) {
            $this->warn("\n⚠️  Some files failed to migrate. Please check the errors above.");
            return 1;
        }

        $this->info("\n✅ Migration process completed!");
        return 0;
    }

    /**
     * Get file contents from local storage.
     */
    private function getFileContents(string $file): ?string
    {
        // 1. Check public disk path (storage/app/public/{file})
        $publicPath = storage_path('app/public/' . $file);
        if (file_exists($publicPath)) {
            return @file_get_contents($publicPath);
        }

        // 2. Check legacy path (storage/app/{file})
        $appPath = storage_path('app/' . $file);
        if (file_exists($appPath)) {
            return @file_get_contents($appPath);
        }

        // 3. Fallback to Storage facade
        try {
            if (Storage::disk('public')->exists($file)) {
                return Storage::disk('public')->get($file);
            }
        } catch (\Throwable $e) {}

        return null;
    }

    /**
     * Delete local file safely from all possible locations.
     */
    private function deleteLocalFile(string $file): void
    {
        $publicPath = storage_path('app/public/' . $file);
        if (file_exists($publicPath)) {
            @unlink($publicPath);
        }

        $appPath = storage_path('app/' . $file);
        if (file_exists($appPath)) {
            @unlink($appPath);
        }
    }

    /**
     * Get local file size in bytes.
     */
    private function getLocalFileSize(string $file): int
    {
        $publicPath = storage_path('app/public/' . $file);
        if (file_exists($publicPath)) {
            return filesize($publicPath) ?: 0;
        }

        $appPath = storage_path('app/' . $file);
        if (file_exists($appPath)) {
            return filesize($appPath) ?: 0;
        }

        return 0;
    }

    /**
     * Get local file modified time (timestamp).
     */
    private function getLocalFileMTime(string $file): int
    {
        $publicPath = storage_path('app/public/' . $file);
        if (file_exists($publicPath)) {
            return filemtime($publicPath) ?: 0;
        }

        $appPath = storage_path('app/' . $file);
        if (file_exists($appPath)) {
            return filemtime($appPath) ?: 0;
        }

        return 0;
    }

    /**
     * Get files from a local directory path.
     */
    private function getLocalFiles(string $localPath, string $prefix): array
    {
        $files = [];
        $iterator = new \RecursiveIteratorIterator(
            new \RecursiveDirectoryIterator($localPath, \RecursiveDirectoryIterator::SKIP_DOTS)
        );

        foreach ($iterator as $file) {
            if ($file->isFile()) {
                $relativePath = $prefix . '/' . $iterator->getSubPathname();
                $files[] = $relativePath;
            }
        }

        return $files;
    }
}
