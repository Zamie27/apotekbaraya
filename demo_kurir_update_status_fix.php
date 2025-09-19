<?php
/**
 * DOKUMENTASI PERBAIKAN FUNGSI UPDATE STATUS KURIR
 * E-Commerce Apotek Baraya
 * 
 * Masalah: Fungsi update status pesanan dari sisi kurir tidak bekerja
 * Solusi: Perbaikan alur konfirmasi dan penambahan opsi update status
 */

// ============================================================================
// PERUBAHAN YANG DILAKUKAN
// ============================================================================

/**
 * 1. PERBAIKAN METHOD showUpdateStatusConfirmation
 * File: app/Livewire/Kurir/DeliveryDetail.php
 * 
 * SEBELUM:
 * - actionMethod: 'showUpdateDelivery' (hanya membuka modal)
 * - confirmButtonClass: 'btn-secondary'
 * - Tidak ada pengaturan nilai default
 * 
 * SESUDAH:
 * - actionMethod: 'updateDelivery' (langsung update status)
 * - confirmButtonClass: 'btn-success'
 * - Pengaturan nilai default untuk deliveryNotes dan newStatus
 * - Pesan konfirmasi yang lebih jelas
 */

// Method yang diperbaiki:
public function showUpdateStatusConfirmation()
{
    // Set default values for quick confirmation
    $this->deliveryNotes = $this->delivery->delivery_notes ?? '';
    $this->newStatus = 'delivered'; // Default to delivered for quick confirmation
    
    $this->dispatch('show-confirmation', [
        'title' => 'Konfirmasi Pengiriman Selesai',
        'message' => 'Apakah Anda yakin pengiriman untuk pesanan ' . $this->delivery->order->order_number . ' telah selesai?',
        'confirmText' => 'Ya, Selesai',
        'cancelText' => 'Batal',
        'confirmButtonClass' => 'btn-success',
        'actionMethod' => 'updateDelivery' // Langsung panggil updateDelivery
    ]);
}

/**
 * 2. PERBAIKAN METHOD updateDelivery
 * File: app/Livewire/Kurir/DeliveryDetail.php
 * 
 * PERUBAHAN:
 * - Foto menjadi opsional untuk status 'delivered' saat dipanggil dari konfirmasi cepat
 * - Perbaikan struktur kondisi untuk status 'failed'
 */

// Bagian yang diperbaiki dalam updateDelivery:
if ($this->newStatus === 'delivered') {
    // Make photo optional for delivered status when called from quick confirmation
    $this->validate([
        'newStatus' => 'required|in:delivered,failed',
        'deliveryNotes' => 'nullable|string|max:500',
        'deliveryPhoto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);
} elseif ($this->newStatus === 'failed') {
    $this->validate([
        'newStatus' => 'required|in:delivered,failed',
        'deliveryNotes' => 'required|string|max:500',
        'deliveryPhoto' => 'nullable|image|mimes:jpeg,png,jpg|max:2048'
    ]);
}

/**
 * 3. PENAMBAHAN METHOD showUpdateDeliveryModal
 * File: app/Livewire/Kurir/DeliveryDetail.php
 * 
 * TUJUAN:
 * - Memberikan opsi untuk update status dengan form lengkap
 * - Memisahkan antara konfirmasi cepat dan update detail
 */

public function showUpdateDeliveryModal()
{
    $this->deliveryNotes = $this->delivery->delivery_notes ?? '';
    $this->newStatus = $this->delivery->status;
    $this->showUpdateModal = true;
}

/**
 * 4. PERBAIKAN UI - TOMBOL UPDATE STATUS
 * File: resources/views/livewire/kurir/delivery-detail.blade.php
 * 
 * SEBELUM:
 * - Satu tombol "Update Status" dengan class btn-secondary
 * 
 * SESUDAH:
 * - Dua tombol:
 *   1. "Selesai" (btn-success btn-sm) - untuk konfirmasi cepat
 *   2. "Detail" (btn-secondary btn-sm) - untuk form lengkap
 */

// UI yang diperbaiki:
?>
<div class="flex gap-2">
    <button 
        wire:click="showUpdateStatusConfirmation" 
        class="btn btn-success btn-sm" 
        wire:loading.attr="disabled">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M5 13l4 4L19 7" />
        </svg>
        <span wire:loading.remove>Selesai</span>
        <span wire:loading>Memproses...</span>
    </button>
    <button 
        wire:click="showUpdateDeliveryModal" 
        class="btn btn-secondary btn-sm" 
        wire:loading.attr="disabled">
        <svg xmlns="http://www.w3.org/2000/svg" class="h-4 w-4 mr-1" fill="none" viewBox="0 0 24 24" stroke="currentColor">
            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z" />
        </svg>
        Detail
    </button>
</div>
<?php

// ============================================================================
// ALUR KERJA SETELAH PERBAIKAN
// ============================================================================

/**
 * OPSI 1: KONFIRMASI CEPAT (Tombol "Selesai")
 * 1. Kurir klik tombol "Selesai"
 * 2. Popup konfirmasi muncul dengan ConfirmationModal
 * 3. Kurir klik "Ya, Selesai"
 * 4. Method updateDelivery dipanggil langsung
 * 5. Status berubah ke "delivered" tanpa perlu foto
 * 6. Order status ikut terupdate
 * 7. Data di-reload dan modal ditutup
 * 
 * OPSI 2: UPDATE DETAIL (Tombol "Detail")
 * 1. Kurir klik tombol "Detail"
 * 2. Modal form lengkap terbuka
 * 3. Kurir pilih status (delivered/failed)
 * 4. Kurir isi catatan dan upload foto (opsional untuk delivered)
 * 5. Kurir klik "Update Status"
 * 6. Validasi form dilakukan
 * 7. Status dan data terupdate
 * 8. Modal ditutup
 */

// ============================================================================
// FILE YANG DIMODIFIKASI
// ============================================================================

/**
 * 1. app/Livewire/Kurir/DeliveryDetail.php
 *    - Method showUpdateStatusConfirmation: Perbaikan actionMethod dan pengaturan default
 *    - Method updateDelivery: Foto opsional untuk status delivered
 *    - Method showUpdateDeliveryModal: Method baru untuk modal form lengkap
 * 
 * 2. resources/views/livewire/kurir/delivery-detail.blade.php
 *    - UI tombol: Dari satu tombol menjadi dua tombol dengan fungsi berbeda
 *    - Style: Menggunakan btn-sm dan warna yang sesuai fungsi
 */

// ============================================================================
// KEUNTUNGAN PERBAIKAN
// ============================================================================

/**
 * 1. FLEKSIBILITAS
 *    - Kurir bisa pilih konfirmasi cepat atau update detail
 *    - Foto tidak wajib untuk pengiriman yang berhasil
 * 
 * 2. USER EXPERIENCE
 *    - Proses lebih cepat untuk pengiriman normal
 *    - Tetap ada opsi lengkap untuk kasus khusus
 *    - UI yang lebih jelas dengan dua tombol berbeda fungsi
 * 
 * 3. KONSISTENSI
 *    - Menggunakan ConfirmationModal yang sama dengan Apoteker
 *    - Style dan behavior yang seragam
 * 
 * 4. RELIABILITY
 *    - Alur yang lebih jelas dan teruji
 *    - Validasi yang tepat sesuai konteks
 *    - Error handling yang lebih baik
 */

// ============================================================================
// TESTING SCENARIO
// ============================================================================

/**
 * 1. TEST KONFIRMASI CEPAT:
 *    - Login sebagai kurir
 *    - Buka detail pengiriman dengan status "in_transit"
 *    - Klik tombol "Selesai"
 *    - Konfirmasi di popup
 *    - Verifikasi status berubah ke "delivered"
 *    - Verifikasi order status ikut terupdate
 * 
 * 2. TEST UPDATE DETAIL:
 *    - Login sebagai kurir
 *    - Buka detail pengiriman dengan status "in_transit"
 *    - Klik tombol "Detail"
 *    - Isi form dengan status "failed" dan catatan
 *    - Submit form
 *    - Verifikasi status dan catatan tersimpan
 * 
 * 3. TEST VALIDASI:
 *    - Coba update dengan status "failed" tanpa catatan
 *    - Verifikasi error validation muncul
 *    - Coba upload foto dengan format salah
 *    - Verifikasi error validation muncul
 */

?>