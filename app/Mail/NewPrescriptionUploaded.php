<?php

namespace App\Mail;

use App\Models\Prescription;
use App\Models\StoreSetting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mail class for sending new prescription upload notification emails to pharmacists
 * 
 * This class handles email notifications when a customer uploads a new prescription
 * that requires pharmacist review and confirmation
 */
class NewPrescriptionUploaded extends Mailable
{
    use SerializesModels;

    public Prescription $prescription;
    public string $title;
    public string $mailMessage;
    public string $actionUrl;
    public string $actionText;
    public ?string $additionalInfo;

    /**
     * Create a new message instance.
     *
     * @param Prescription $prescription The prescription instance that was uploaded
     */
    public function __construct(Prescription $prescription)
    {
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
            subject: 'Resep Baru Perlu Ditinjau - ' . $this->prescription->prescription_number,
        );
    }

    /**
     * Get the message content definition.
     */
    public function content(): Content
    {
        return new Content(
            view: 'emails.prescription-notification',
            with: [
                'prescription' => $this->prescription,
                'title' => $this->title,
                'mailMessage' => $this->mailMessage,
                'actionUrl' => $this->actionUrl,
                'actionText' => $this->actionText,
                'additionalInfo' => $this->additionalInfo,
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
     * Set notification content for new prescription upload
     */
    private function setNotificationContent(): void
    {
        $this->title = 'Resep Baru Memerlukan Tinjauan! 📋';
        $this->mailMessage = 'Resep baru telah diunggah oleh pelanggan dan memerlukan tinjauan dari apoteker. Silakan periksa detail resep dan lakukan konfirmasi.';
        $this->actionUrl = route('apoteker.prescriptions.detail', $this->prescription->prescription_id);
        $this->actionText = 'Tinjau Resep Sekarang';
        $this->additionalInfo = 'Resep ini diunggah oleh ' . $this->prescription->user->name . ' untuk pasien ' . $this->prescription->patient_name . '. Metode pengambilan: ' . ($this->prescription->delivery_method === 'delivery' ? 'Kirim ke Alamat' : 'Ambil di Apotek') . '.';
    }
}