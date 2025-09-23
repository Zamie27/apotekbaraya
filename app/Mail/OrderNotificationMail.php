<?php

namespace App\Mail;

use App\Models\Order;
use App\Models\StoreSetting;
use Illuminate\Mail\Mailable;
use Illuminate\Mail\Mailables\Content;
use Illuminate\Mail\Mailables\Envelope;
use Illuminate\Queue\SerializesModels;

/**
 * Mail class for sending order status notification emails to customers
 * 
 * This class handles email notifications for various order status changes
 * including payment confirmation, order processing, shipping updates, and completion
 * 
 * Note: Removed ShouldQueue to send emails directly like password reset
 */
class OrderNotificationMail extends Mailable
{
    use SerializesModels;

    public Order $order;
    public string $notificationType;
    public string $title;
    public string $mailMessage;
    public string $statusLabel;
    public ?string $actionUrl;
    public ?string $actionText;
    public ?string $additionalInfo;

    /**
     * Create a new message instance.
     *
     * @param Order $order The order instance
     * @param string $notificationType Type of notification (paid, confirmed, ready_to_ship, etc.)
     * @param array $customData Additional data for customization
     */
    public function __construct(
        Order $order, 
        string $notificationType, 
        array $customData = []
    ) {
        $this->order = $order;
        $this->notificationType = $notificationType;
        
        // Set default values based on notification type
        $this->setNotificationContent($customData);
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
            view: 'emails.order-notification',
            with: [
                'order' => $this->order,
                'title' => $this->title,
                'mailMessage' => $this->mailMessage,
                'statusLabel' => $this->statusLabel,
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
     * Set notification content based on type
     *
     * @param array $customData Custom data to override defaults
     */
    private function setNotificationContent(array $customData = []): void
    {
        $defaults = $this->getDefaultContent();
        
        $this->title = $customData['title'] ?? $defaults['title'];
        $this->mailMessage = $customData['message'] ?? $defaults['message'];
        $this->statusLabel = $customData['statusLabel'] ?? $defaults['statusLabel'];
        $this->actionUrl = $customData['actionUrl'] ?? $defaults['actionUrl'];
        $this->actionText = $customData['actionText'] ?? $defaults['actionText'];
        $this->additionalInfo = $customData['additionalInfo'] ?? $defaults['additionalInfo'];
    }

    /**
     * Get default content based on notification type
     *
     * @return array
     */
    private function getDefaultContent(): array
    {
        $orderUrl = route('pelanggan.orders.show', $this->order->order_id);
        
        return match ($this->notificationType) {
            'paid' => [
                'title' => 'Pembayaran Berhasil Diterima! 💳',
                'message' => 'Terima kasih! Pembayaran untuk pesanan Anda telah berhasil kami terima dan sedang dalam proses verifikasi. Tim kami akan segera memproses pesanan Anda.',
                'statusLabel' => 'Sudah Dibayar',
                'actionUrl' => $orderUrl,
                'actionText' => 'Lihat Detail Pesanan',
                'additionalInfo' => 'Pesanan Anda akan segera diproses setelah pembayaran dikonfirmasi oleh tim kami.',
            ],
            
            'confirmed' => [
                'title' => 'Pesanan Dikonfirmasi! ✅',
                'message' => 'Kabar baik! Pesanan Anda telah dikonfirmasi dan sedang dipersiapkan oleh apoteker kami. Kami akan segera memproses obat-obatan yang Anda pesan.',
                'statusLabel' => 'Dikonfirmasi',
                'actionUrl' => $orderUrl,
                'actionText' => 'Lihat Detail Pesanan',
                'additionalInfo' => 'Estimasi waktu persiapan: 1-2 jam kerja. Anda akan mendapat notifikasi ketika pesanan siap.',
            ],
            
            'processing' => [
                'title' => 'Pesanan Sedang Diproses! ⚙️',
                'message' => 'Pesanan Anda sedang dalam proses persiapan oleh tim apoteker kami. Obat-obatan sedang disiapkan dan dikemas dengan teliti untuk memastikan kualitas terbaik.',
                'statusLabel' => 'Sedang Diproses',
                'actionUrl' => $orderUrl,
                'actionText' => 'Lihat Detail Pesanan',
                'additionalInfo' => 'Proses persiapan membutuhkan waktu 30-60 menit. Anda akan mendapat notifikasi ketika pesanan siap.',
            ],
            
            'ready_to_ship' => [
                'title' => 'Pesanan Siap Dikirim! 🚚',
                'message' => 'Pesanan Anda telah selesai dipersiapkan dan siap untuk dikirim. Kurir kami akan segera mengirimkan pesanan ke alamat yang telah Anda tentukan.',
                'statusLabel' => 'Siap Dikirim',
                'actionUrl' => $orderUrl,
                'actionText' => 'Lacak Pengiriman',
                'additionalInfo' => 'Estimasi waktu pengiriman: 1-3 jam. Pastikan ada yang menerima di alamat tujuan.',
            ],
            
            'ready_for_pickup' => [
                'title' => 'Pesanan Siap Diambil! 🏪',
                'message' => 'Pesanan Anda telah selesai dipersiapkan dan siap untuk diambil di apotek. Silakan datang ke apotek kami dengan membawa bukti pesanan ini.',
                'statusLabel' => 'Siap Diambil',
                'actionUrl' => $orderUrl,
                'actionText' => 'Lihat Detail Pesanan',
                'additionalInfo' => 'Jam operasional: Senin-Sabtu 08:00-20:00, Minggu 09:00-17:00. Jangan lupa bawa identitas diri.',
            ],
            
            'shipped' => [
                'title' => 'Pesanan Sedang Dikirim! 🛵',
                'message' => 'Pesanan Anda sedang dalam perjalanan menuju alamat tujuan. Kurir kami akan segera tiba di lokasi Anda.',
                'statusLabel' => 'Sedang Dikirim',
                'actionUrl' => $orderUrl,
                'actionText' => 'Lacak Pengiriman',
                'additionalInfo' => 'Kurir akan menghubungi Anda sebelum tiba. Pastikan nomor telepon dapat dihubungi.',
            ],
            
            'picked_up' => [
                'title' => 'Pesanan Telah Diambil! ✅',
                'message' => 'Terima kasih! Pesanan Anda telah berhasil diambil di apotek. Semoga obat-obatan yang Anda beli bermanfaat untuk kesehatan.',
                'statusLabel' => 'Telah Diambil',
                'actionUrl' => $orderUrl,
                'actionText' => 'Lihat Detail Pesanan',
                'additionalInfo' => 'Jangan lupa untuk mengikuti petunjuk penggunaan obat yang tertera pada kemasan.',
            ],
            
            'delivered' => [
                'title' => 'Pesanan Telah Sampai! 📦',
                'message' => 'Pesanan Anda telah berhasil dikirim dan diterima di alamat tujuan. Terima kasih telah mempercayai Apotek Baraya untuk kebutuhan kesehatan Anda.',
                'statusLabel' => 'Telah Dikirim',
                'actionUrl' => $orderUrl,
                'actionText' => 'Berikan Ulasan',
                'additionalInfo' => 'Jangan lupa untuk mengikuti petunjuk penggunaan obat yang tertera pada kemasan.',
            ],
            
            'completed' => [
                'title' => 'Pesanan Selesai! 🎉',
                'message' => 'Transaksi Anda telah selesai dengan sempurna. Terima kasih telah berbelanja di Apotek Baraya. Kami berharap dapat melayani Anda kembali di masa mendatang.',
                'statusLabel' => 'Selesai',
                'actionUrl' => route('home'),
                'actionText' => 'Belanja Lagi',
                'additionalInfo' => 'Dapatkan poin reward untuk setiap pembelian. Kumpulkan poin untuk mendapatkan diskon menarik!',
            ],
            
            'cancelled' => [
                'title' => 'Pesanan Dibatalkan ❌',
                'message' => 'Pesanan Anda telah dibatalkan sesuai permintaan. Jika Anda telah melakukan pembayaran, dana akan dikembalikan dalam 1-3 hari kerja.',
                'statusLabel' => 'Dibatalkan',
                'actionUrl' => route('home'),
                'actionText' => 'Belanja Lagi',
                'additionalInfo' => 'Jika ada pertanyaan mengenai pembatalan atau pengembalian dana, silakan hubungi customer service kami.',
            ],
            
            default => [
                'title' => 'Update Status Pesanan',
                'message' => 'Status pesanan Anda telah diperbarui. Silakan cek detail pesanan untuk informasi lebih lanjut.',
                'statusLabel' => $this->order->status_label,
                'actionUrl' => $orderUrl,
                'actionText' => 'Lihat Detail Pesanan',
                'additionalInfo' => null,
            ],
        };
    }

    /**
     * Get email subject based on notification type
     *
     * @return string
     */
    private function getSubject(): string
    {
        $orderNumber = $this->order->order_number;
        
        return match ($this->notificationType) {
            'paid' => "Pembayaran Diterima - Pesanan #{$orderNumber}",
            'confirmed' => "Pesanan Dikonfirmasi - #{$orderNumber}",
            'processing' => "Pesanan Sedang Diproses - #{$orderNumber}",
            'ready_to_ship' => "Pesanan Siap Dikirim - #{$orderNumber}",
            'ready_for_pickup' => "Pesanan Siap Diambil - #{$orderNumber}",
            'shipped' => "Pesanan Sedang Dikirim - #{$orderNumber}",
            'picked_up' => "Pesanan Telah Diambil - #{$orderNumber}",
            'delivered' => "Pesanan Telah Sampai - #{$orderNumber}",
            'completed' => "Pesanan Selesai - #{$orderNumber}",
            'cancelled' => "Pesanan Dibatalkan - #{$orderNumber}",
            default => "Update Pesanan - #{$orderNumber}",
        };
    }

    /**
     * Get formatted complete address from store settings
     *
     * @return string
     */
    private function getFormattedAddress(): string
    {
        $addressParts = [
            StoreSetting::get('store_address', 'Jl. Raya Apotek No. 123'),
            StoreSetting::get('store_village', ''),
            StoreSetting::get('store_district', ''),
            StoreSetting::get('store_regency', ''),
            StoreSetting::get('store_province', ''),
            StoreSetting::get('store_postal_code', ''),
        ];

        // Filter out empty parts and join with comma
        $filteredParts = array_filter($addressParts, function($part) {
            return !empty(trim($part));
        });

        return implode(', ', $filteredParts);
    }
}