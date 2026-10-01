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
        if (Schema::hasTable('provider_address_mappings') && !Schema::hasColumn('provider_address_mappings', 'deleted_at')) {
            Schema::table('provider_address_mappings', function (Blueprint $table) {
                $table->softDeletes();
            });
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        if (Schema::hasTable('provider_address_mappings') && Schema::hasColumn('provider_address_mappings', 'deleted_at')) {
            Schema::table('provider_address_mappings', function (Blueprint $table) {
                $table->dropSoftDeletes();
            });
        }
    }
};
