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
        $tables = [
            'banks',
            'banner_payments',
            'booking_activities',
            'booking_address_mappings',
            'commission_earnings',
            'documents',
            'handyman_ratings',
            'mail_template_content_mappings',
            'mail_templates',
            'notification_templates',
            'provider_documents',
            'service_zones',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (!Schema::hasColumn($tableName, 'deleted_at')) {
                        $table->softDeletes();
                    }
                });
            }
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        $tables = [
            'banks',
            'banner_payments',
            'booking_activities',
            'booking_address_mappings',
            'commission_earnings',
            'documents',
            'handyman_ratings',
            'mail_template_content_mappings',
            'mail_templates',
            'notification_templates',
            'provider_documents',
            'service_zones',
        ];

        foreach ($tables as $tableName) {
            if (Schema::hasTable($tableName)) {
                Schema::table($tableName, function (Blueprint $table) use ($tableName) {
                    if (Schema::hasColumn($tableName, 'deleted_at')) {
                        $table->dropSoftDeletes();
                    }
                });
            }
        }
    }
};
