<?php

namespace App\Mail;

use App\Models\Surat;
use Illuminate\Bus\Queueable;
use Illuminate\Mail\Mailables\Address;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class LegalisirDiambil extends Mailable
{
    use Queueable, SerializesModels;
    public Surat $surat;

    /**
     * Create a new message instance.
     */
    public function __construct(Surat $surat)
    {
        $this->surat = $surat;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            from: new Address(env('MAIL_FROM_ADDRESS') ? env('MAIL_FROM_ADDRESS') : 'fkipunivbengkulu@gmail.com', 'FKIP UNIB'),
            subject: 'Berkas legalisir ijazah UNIB Anda telah siap diambil!',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            markdown: 'emails.legalisir-diambil',
        );
    }

    /**
     * Get the attachments for the message.
     *
     * @return array<int, \Illuminate\Mail\Mailables\Attachment>
     */
    public function attachments(): array
    {
        return [];
    }
}
