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
        Schema::table('orders', function (Blueprint $table) {
            // Add receipt confirmation photo column
            $table->string('receipt_photo')->nullable()->after('confirmed_by');
            
            // Add delivery/receipt photo column
            $table->string('delivery_photo')->nullable()->after('receipt_photo');
            
            // Add uploaded by columns for tracking who uploaded the photos
            $table->unsignedBigInteger('receipt_photo_uploaded_by')->nullable()->after('receipt_photo');
            $table->unsignedBigInteger('delivery_photo_uploaded_by')->nullable()->after('delivery_photo');
            
            // Add upload timestamps
            $table->timestamp('receipt_photo_uploaded_at')->nullable()->after('receipt_photo_uploaded_by');
            $table->timestamp('delivery_photo_uploaded_at')->nullable()->after('delivery_photo_uploaded_by');
            
            // Add foreign key constraints
            $table->foreign('receipt_photo_uploaded_by')->references('user_id')->on('users')->onDelete('set null');
            $table->foreign('delivery_photo_uploaded_by')->references('user_id')->on('users')->onDelete('set null');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('orders', function (Blueprint $table) {
            // Drop foreign key constraints first
            $table->dropForeign(['receipt_photo_uploaded_by']);
            $table->dropForeign(['delivery_photo_uploaded_by']);
            
            // Drop columns
            $table->dropColumn([
                'receipt_photo',
                'delivery_photo',
                'receipt_photo_uploaded_by',
                'delivery_photo_uploaded_by',
                'receipt_photo_uploaded_at',
                'delivery_photo_uploaded_at'
            ]);
        });
    }
};
