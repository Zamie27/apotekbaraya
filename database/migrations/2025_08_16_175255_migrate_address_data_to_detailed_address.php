<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Run the migrations.
     * Migrate data from old 'address' field to 'detailed_address' field
     */
    public function up(): void
    {
        // Since 'address' field may not exist, we'll set a default value for detailed_address
        // This migration will be used to set default values for new fields
        DB::statement("
            UPDATE user_addresses 
            SET detailed_address = CONCAT(district, ', ', city, ' ', postal_code) 
            WHERE (detailed_address IS NULL OR detailed_address = '')
        ");
        
        // Set default village value for existing records where village is empty
        DB::statement("
            UPDATE user_addresses 
            SET village = 'Tidak Diketahui' 
            WHERE (village IS NULL OR village = '') 
        ");
    }

    /**
     * Reverse the migrations.
     * This migration is irreversible as we're consolidating data
     */
    public function down(): void
    {
        // This migration is not reversible as we're consolidating data
        // The 'address' field will be restored by the next migration's rollback
    }
};
