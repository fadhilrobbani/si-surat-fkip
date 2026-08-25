<?php

namespace App\Services;

use Illuminate\Support\Facades\Storage;

class StorageHelper
{
    /**
     * Get the configured storage disk name.
     * Returns 'r2' if configured, otherwise falls back to 'public'.
     */
    public static function disk(): string
    {
        return config('filesystems.default', 'public');
    }

    /**
     * Get file contents from the configured storage disk.
     * Used for embedding images (TTD/stempel) in PDF templates.
     */
    public static function getFileContents(string $path): ?string
    {
        $disk = static::disk();

        // Try R2/cloud disk first
        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->get($path);
        }

        // Fallback: try 'public' disk (for files not yet migrated)
        if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->get($path);
        }

        // Fallback: try local path (legacy)
        $localPath = public_path('storage/' . $path);
        if (file_exists($localPath)) {
            return file_get_contents($localPath);
        }

        return null;
    }

    /**
     * Get the public URL for a file.
     * For R2: returns the R2 public URL.
     * For local: returns asset('storage/...') URL.
     */
    public static function url(string $path): string
    {
        $disk = static::disk();

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->url($path);
        }

        // Fallback to local
        if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
            return asset('storage/' . $path);
        }

        // Last resort: return asset URL anyway
        return asset('storage/' . $path);
    }

    /**
     * Check if a file exists on the configured storage disk.
     */
    public static function exists(string $path): bool
    {
        $disk = static::disk();

        if (Storage::disk($disk)->exists($path)) {
            return true;
        }

        // Fallback: check 'public' disk
        if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
            return true;
        }

        return false;
    }

    /**
     * Get the MIME type of a file with automatic fallback by extension.
     */
    public static function mimeType(string $path): string
    {
        $disk = static::disk();

        try {
            if (Storage::disk($disk)->exists($path)) {
                $mime = Storage::disk($disk)->mimeType($path);
                if ($mime && $mime !== 'application/octet-stream') {
                    return $mime;
                }
            }
        } catch (\Throwable $e) {}

        try {
            if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
                $mime = Storage::disk('public')->mimeType($path);
                if ($mime && $mime !== 'application/octet-stream') {
                    return $mime;
                }
            }
        } catch (\Throwable $e) {}

        $localPath = storage_path('app/' . $path);
        if (file_exists($localPath)) {
            $mime = @mime_content_type($localPath);
            if ($mime && $mime !== 'application/octet-stream') {
                return $mime;
            }
        }

        $ext = strtolower(pathinfo($path, PATHINFO_EXTENSION));
        $map = [
            'pdf'  => 'application/pdf',
            'png'  => 'image/png',
            'jpg'  => 'image/jpeg',
            'jpeg' => 'image/jpeg',
            'webp' => 'image/webp',
            'gif'  => 'image/gif',
            'svg'  => 'image/svg+xml',
        ];

        return $map[$ext] ?? 'application/pdf';
    }

    /**
     * Store a file to the configured storage disk.
     */
    public static function put(string $path, $contents): bool
    {
        return Storage::disk(static::disk())->put($path, $contents);
    }

    /**
     * Get a file response (for inline preview in browser) from the configured disk.
     */
    public static function response(string $path, array $headers = [])
    {
        $disk = static::disk();
        $mime = $headers['Content-Type'] ?? static::mimeType($path);
        $filename = basename($path);

        $defaultHeaders = [
            'Content-Type' => $mime,
            'Content-Disposition' => 'inline; filename="' . $filename . '"',
        ];
        $mergedHeaders = array_merge($defaultHeaders, $headers);

        if (Storage::disk($disk)->exists($path)) {
            return Storage::disk($disk)->response($path, $filename, $mergedHeaders, 'inline');
        }

        // Fallback to public disk
        if ($disk !== 'public' && Storage::disk('public')->exists($path)) {
            return Storage::disk('public')->response($path, $filename, $mergedHeaders, 'inline');
        }

        // Fallback to local file
        $localPath = storage_path('app/' . $path);
        if (file_exists($localPath)) {
            return response()->file($localPath, $mergedHeaders);
        }

        abort(404, 'File tidak ditemukan');
    }
}
