<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->string('prescription_number')->unique()->after('prescription_id'); // Nomor resep unik
            $table->string('doctor_name')->after('user_id'); // Nama dokter
            $table->string('patient_name')->after('doctor_name'); // Nama pasien
            $table->text('notes')->nullable()->after('patient_name'); // Catatan tambahan dari pelanggan
            $table->string('prescription_image')->after('notes'); // Path file gambar resep
            $table->enum('status', ['pending', 'confirmed', 'rejected', 'processed'])->default('pending')->change();
            $table->foreignId('confirmed_by')->nullable()->after('status')->constrained('users', 'user_id')->onDelete('set null'); // Apoteker yang konfirmasi
            $table->text('confirmation_notes')->nullable()->after('confirmed_by'); // Catatan dari apoteker
            $table->timestamp('confirmed_at')->nullable()->after('confirmation_notes'); // Waktu konfirmasi
            $table->foreignId('order_id')->nullable()->after('confirmed_at')->constrained('orders', 'order_id')->onDelete('set null'); // Order yang dibuat dari resep
            $table->timestamp('updated_at')->nullable()->after('created_at'); // Add updated_at
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('prescriptions', function (Blueprint $table) {
            $table->dropColumn([
                'prescription_number',
                'doctor_name', 
                'patient_name',
                'notes',
                'prescription_image',
                'confirmed_by',
                'confirmation_notes',
                'confirmed_at',
                'order_id',
                'updated_at'
            ]);
            $table->enum('status', ['pending', 'approved', 'rejected', 'completed'])->default('pending')->change();
        });
    }
};
