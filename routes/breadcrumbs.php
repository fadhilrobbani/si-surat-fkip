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

Breadcrumbs::for('staff-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/staff/pengajuan-surat');
});

Breadcrumbs::for('staff-pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('staff-pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('staff-show-form-surat', $jenisSurat));
});

Breadcrumbs::for('staff-dekan-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Pilih Surat', '/staff-dekan/pengajuan-surat');
});

Breadcrumbs::for('staff-dekan-pengajuan-surat-form', function (BreadcrumbTrail $trail, JenisSurat $jenisSurat) {
    $trail->parent('staff-dekan-pengajuan-surat');
    $trail->push('Form Pengajuan Surat', route('staff-dekan-show-form-surat', $jenisSurat));
});


Breadcrumbs::for('riwayat-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Pengajuan', '/mahasiswa/riwayat-pengajuan-surat');
});

Breadcrumbs::for('show-pengajuan-surat', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('riwayat-pengajuan-surat');
    $trail->push('Detail Pengajuan', route('lihat-surat-mahasiswa', $surat));
});

Breadcrumbs::for('staff-riwayat-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Pengajuan Surat', '/staff/riwayat-pengajuan-surat');
});

Breadcrumbs::for('staff-show-pengajuan-surat', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('staff-riwayat-pengajuan-surat');
    $trail->push('Detail Pengajuan Surat', route('show-detail-pengajuan-surat-staff', $surat));
});


Breadcrumbs::for('staff-dekan-riwayat-pengajuan-surat', function (BreadcrumbTrail $trail) {
    $trail->push('Riwayat Pengajuan Surat', '/staff-dekan/riwayat-pengajuan-surat');
});

Breadcrumbs::for('staff-dekan-show-pengajuan-surat', function (BreadcrumbTrail $trail, Surat $surat) {
    $trail->parent('staff-dekan-riwayat-pengajuan-surat');
    $trail->push('Detail Pengajuan Surat', route('show-detail-pengajuan-surat-staff-dekan', $surat));
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
