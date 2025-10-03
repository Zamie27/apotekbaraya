<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\Prescription;
use App\Models\StoreSetting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mail class for sending prescription order creation notification emails
 * 
 * This class handles email notifications when an order is created from a prescription
 * by the pharmacist, informing the customer about the order details and next steps
 */
class PrescriptionOrderCreated extends Mailable
{
    use SerializesModels;

    public Order $order;
    public Prescription $prescription;
    public string $title;
    public string $mailMessage;
    public string $actionUrl;
    public string $actionText;
    public ?string $additionalInfo;

    /**
     * Create a new message instance.
     *
     * @param Order $order The order instance created from prescription
     * @param Prescription $prescription The prescription instance
     */
    public function __construct(Order $order, Prescription $prescription)
    {
        $this->order = $order;
        $this->prescription = $prescription;
        
        // Set notification content
        $this->setNotificationContent();
    }

    /**
     * Get the message envelope.
     */
    public function envelope(): Envelope
    {
        return new Envelope(
            subject: $this->getSubject(),
            from: config('mail.from.address', 'noreply@apotekbaraya.com'),
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        // Get store settings for footer
        $storeSettings = [
            'store_name' => StoreSetting::get('store_name', 'Apotek Baraya'),
            'store_phone' => StoreSetting::get('store_phone', '(022) 1234-5678'),
            'store_email' => StoreSetting::get('store_email', 'info@apotekbaraya.com'),
            'store_address' => $this->getFormattedAddress(),
        ];

        return new Content(
            view: 'emails.prescription-order-created',
            with: [
                'order' => $this->order,
                'prescription' => $this->prescription,
                'title' => $this->title,
                'mailMessage' => $this->mailMessage,
                'actionUrl' => $this->actionUrl,
                'actionText' => $this->actionText,
                'additionalInfo' => $this->additionalInfo,
                'subject' => $this->getSubject(),
                'storeSettings' => $storeSettings,
            ]
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

    /**
     * Set notification content for prescription order creation
     */
    private function setNotificationContent(): void
    {
        $this->title = 'Pesanan Berhasil Dibuat dari Resep! 💊';
        $this->mailMessage = 'Kabar baik! Resep Anda telah diproses oleh apoteker kami dan pesanan telah berhasil dibuat. Silakan lakukan pembayaran untuk melanjutkan proses pesanan.';
        $this->actionUrl = route('pelanggan.orders.show', $this->order->order_id);
        $this->actionText = 'Lihat Detail Pesanan & Bayar';
        $this->additionalInfo = 'Pesanan ini dibuat berdasarkan resep yang Anda upload. Pastikan untuk melakukan pembayaran dalam 24 jam agar pesanan tidak dibatalkan otomatis.';
    }

    /**
     * Get email subject
     *
     * @return string
     */
    private function getSubject(): string
    {
        return "Pesanan dari Resep Berhasil Dibuat - #{$this->order->order_number}";
    }

    /**
     * Get formatted store address
     *
     * @return string
     */
    private function getFormattedAddress(): string
    {
        $address = StoreSetting::get('store_address', 'Jl. Contoh No. 123');
        $village = StoreSetting::get('store_village', 'Kelurahan Contoh');
        $district = StoreSetting::get('store_district', 'Kecamatan Contoh');
        $regency = StoreSetting::get('store_regency', 'Kota Contoh');
        $province = StoreSetting::get('store_province', 'Jawa Barat');
        $postalCode = StoreSetting::get('store_postal_code', '40123');
        
        return "{$address}, {$village}, {$district}, {$regency}, {$province} {$postalCode}";
    }
}