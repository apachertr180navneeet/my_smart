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
        Schema::table('payment_gateways', function (Blueprint $table) {
            if (Schema::hasColumn('payment_gateways', 'name')) {
                $table->string('name')->nullable()->change();
            }
            if (!Schema::hasColumn('payment_gateways', 'title')) {
                $table->string('title')->nullable();
            }
            if (!Schema::hasColumn('payment_gateways', 'type')) {
                $table->string('type')->nullable();
            }
            if (!Schema::hasColumn('payment_gateways', 'status')) {
                $table->tinyInteger('status')->default(1);
            }
            if (!Schema::hasColumn('payment_gateways', 'is_test')) {
                $table->tinyInteger('is_test')->default(1);
            }
            if (!Schema::hasColumn('payment_gateways', 'value')) {
                $table->longText('value')->nullable();
            }
            if (!Schema::hasColumn('payment_gateways', 'live_value')) {
                $table->longText('live_value')->nullable();
            }
        });

        if (\DB::table('payment_gateways')->count() == 0) {
            $seeder = new \Database\Seeders\PaymentGatewaysTableSeeder();
            $seeder->run();
        }
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        //
    }
};
