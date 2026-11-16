<?php

namespace Tests\Feature;

use App\Services\EmailNotifikasi;
use Illuminate\Mail\Mailable;
use Illuminate\Support\Facades\Mail;
use Symfony\Component\Mailer\Exception\TransportException;
use Tests\TestCase;

class EmailNotifikasiTest extends TestCase
{
    public function test_default_mati_tidak_mengirim_email(): void
    {
        Mail::fake();

        EmailNotifikasi::kirim('pemohon@example.test', new Mailable());

        Mail::assertNothingSent();
    }

    public function test_smtp_gagal_tidak_menggagalkan_aksi_dan_memberi_peringatan(): void
    {
        config(['mail.notifikasi_surat' => true]);
        Mail::shouldReceive('to')->andReturnSelf();
        Mail::shouldReceive('send')->andThrow(new TransportException('550 daily quota exceeded'));

        EmailNotifikasi::kirim('pemohon@example.test', new Mailable());

        $this->assertSame(EmailNotifikasi::PESAN_GAGAL, session('deleted'));
    }

    public function test_smtp_normal_tanpa_peringatan(): void
    {
        config(['mail.notifikasi_surat' => true]);
        Mail::fake();

        EmailNotifikasi::kirim('pemohon@example.test', new Mailable());

        Mail::assertSent(Mailable::class);
        $this->assertNull(session('deleted'));
    }
}
