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
        Schema::table('user_logs', function (Blueprint $table) {
            // Add missing columns if they don't exist
            if (!Schema::hasColumn('user_logs', 'user_id')) {
                $table->unsignedBigInteger('user_id')->nullable()->after('id');
            }
            if (!Schema::hasColumn('user_logs', 'action')) {
                $table->string('action')->after('user_id');
            }
            if (!Schema::hasColumn('user_logs', 'description')) {
                $table->text('description')->nullable()->after('action');
            }
            if (!Schema::hasColumn('user_logs', 'details')) {
                $table->json('details')->nullable()->after('description');
            }
            if (!Schema::hasColumn('user_logs', 'created_at')) {
                $table->timestamps();
            }
            
            // Add indexes if they don't exist
            if (!Schema::hasIndex('user_logs', 'user_logs_action_index')) {
                $table->index('action');
            }
            if (!Schema::hasIndex('user_logs', 'user_logs_created_at_index')) {
                $table->index('created_at');
            }
            if (!Schema::hasIndex('user_logs', 'user_logs_user_id_index')) {
                $table->index('user_id');
            }
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::table('user_logs', function (Blueprint $table) {
            // Drop indexes first
            $table->dropIndex(['action']);
            $table->dropIndex(['created_at']);
            $table->dropIndex(['user_id']);
            
            // Drop columns
            $table->dropColumn(['user_id', 'action', 'description', 'details']);
            $table->dropTimestamps();
        });
    }
};
