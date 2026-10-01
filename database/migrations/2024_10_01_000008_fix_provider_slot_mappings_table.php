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
        if (Schema::hasTable('provider_slot_mappings')) {
            Schema::table('provider_slot_mappings', function (Blueprint $table) {
                if (!Schema::hasColumn('provider_slot_mappings', 'days')) {
                    $table->string('days')->nullable()->after('provider_id');
                }
                if (!Schema::hasColumn('provider_slot_mappings', 'start_at')) {
                    $table->time('start_at')->nullable()->after('days');
                }
                if (!Schema::hasColumn('provider_slot_mappings', 'end_at')) {
                    $table->time('end_at')->nullable()->after('start_at');
                }
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('provider_slot_mappings')) {
            Schema::table('provider_slot_mappings', function (Blueprint $table) {
                if (Schema::hasColumn('provider_slot_mappings', 'days')) {
                    $table->dropColumn('days');
                }
                if (Schema::hasColumn('provider_slot_mappings', 'start_at')) {
                    $table->dropColumn('start_at');
                }
                if (Schema::hasColumn('provider_slot_mappings', 'end_at')) {
                    $table->dropColumn('end_at');
                }
            });
        }
    }
};
