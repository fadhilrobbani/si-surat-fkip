<?php // routes/breadcrumbs.php

// Note: Laravel will automatically resolve `Breadcrumbs::` without
// this import. This is nice for IDE syntax and refactoring.

use App\Models\Approval;
use App\Models\Surat;

// This import is also not required, and you could replace `BreadcrumbTrail $trail`
//  with `$trail`. This is nice for IDE type checking and completion.
use App\Models\JenisSurat;
use Diglactic\Breadcrumbs\Breadcrumbs;
use Diglactic\Breadcrumbs\Generator as BreadcrumbTrail;

Breadcrumbs::for('pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/mahasiswa/pengajuan-surat');
});

Breadcrumbs::for('pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('show-form-surat', $jenisSurat));
});

Breadcrumbs::for('riwayat-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Pengajuan Surat', '/mahasiswa/riwayat-pengajuan-surat');
});

Breadcrumbs::for('show-pengajuan-surat', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('riwayat-pengajuan-surat');
    $trail->push('Detail Pengajuan Surat', route('lihat-surat-mahasiswa', $surat));
});

Breadcrumbs::for('surat-masuk', function (BreadcrumbTrail $trail) {
    $trail->push('Surat Masuk', '/' . auth()->user()->role->name . '/surat-masuk');
});

Breadcrumbs::for('detail-surat-masuk', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('surat-masuk');
    $trail->push('Detail Surat', '/' . auth()->user()->role->name . '/surat-masuk' . '/show' . '/' . $surat->id);
});

Breadcrumbs::for('riwayat-persetujuan', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Persetujuan', '/' . auth()->user()->role->name . '/riwayat-persetujuan');
});

Breadcrumbs::for('detail-persetujuan', function (BreadcrumbTrail $trail, Approval $approval) {
    $trail->parent('riwayat-persetujuan');
    $trail->push('Detail Surat', '/' . auth()->user()->role->name . '/riwayat-persetujuan' . '/show' . '/' . $approval->id);
});
