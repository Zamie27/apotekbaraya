<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Prescription;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

class OrderCreatedFromPrescription extends Mailable
{
    use Queueable, SerializesModels;

    public $order;
    public $prescription;

    /**
     * Create a new message instance.
     */
    public function __construct(Order $order, Prescription $prescription)
    {
        $this->order = $order;
        $this->prescription = $prescription;
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: 'Pesanan Berhasil Dibuat dari Resep - Apotek Baraya',
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.order-created-from-prescription',
            with: [
                'order' => $this->order,
                'prescription' => $this->prescription,
            ],
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
