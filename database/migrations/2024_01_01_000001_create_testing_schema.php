<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        // Skip testing schema if baseline tables already exist (e.g. existing MySQL installation)
        if (Schema::hasTable('roles') || Schema::hasTable('users')) {
            return;
        }

        if (!Schema::hasTable('roles')) {
            Schema::create('roles', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('permissions')) {
            Schema::create('permissions', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('guard_name');
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('role_has_permissions')) {
            Schema::create('role_has_permissions', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->primary(['role_id', 'permission_id']);
            });
        }

        if (!Schema::hasTable('model_has_roles')) {
            Schema::create('model_has_roles', function (Blueprint $table) {
            $table->foreignId('role_id')->constrained()->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['role_id', 'model_type', 'model_id']);
            $table->index(['model_type', 'model_id']);
            });
        }

        if (!Schema::hasTable('model_has_permissions')) {
            Schema::create('model_has_permissions', function (Blueprint $table) {
            $table->foreignId('permission_id')->constrained()->cascadeOnDelete();
            $table->string('model_type');
            $table->unsignedBigInteger('model_id');
            $table->primary(['permission_id', 'model_type', 'model_id']);
            $table->index(['model_type', 'model_id']);
            });
        }

        if (!Schema::hasTable('countries')) {
            Schema::create('countries', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('shortname')->nullable();
            $table->string('phonecode')->nullable();
            $table->string('currency')->nullable();
            $table->string('currency_symbol')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('states')) {
            Schema::create('states', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('country_id');
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('cities')) {
            Schema::create('cities', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('state_id');
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('users')) {
            Schema::create('users', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('first_name')->nullable();
            $table->string('last_name')->nullable();
            $table->string('display_name')->nullable();
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('remember_token')->nullable();
            $table->string('username')->nullable()->unique();
            $table->string('contact_number')->nullable();
            $table->string('profile_image')->nullable();
            $table->string('address')->nullable();
            $table->string('latitude')->nullable();
            $table->string('longitude')->nullable();
            $table->string('user_type')->default('user');
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('country_id')->nullable();
            $table->unsignedBigInteger('state_id')->nullable();
            $table->unsignedBigInteger('city_id')->nullable();
            $table->unsignedBigInteger('provider_type_id')->nullable();
            $table->unsignedBigInteger('handyman_type_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('service_address_id')->nullable();
            $table->unsignedBigInteger('handyman_zone_id')->nullable();
            $table->boolean('is_subscribe')->default(false);
            $table->text('description')->nullable();
            $table->string('player_id')->nullable();
            $table->integer('is_featured')->default(0);
            $table->string('time_zone')->nullable()->default('UTC');
            $table->string('last_notification_seen')->nullable();
            $table->string('login_type')->nullable();
            $table->string('uid')->nullable();
            $table->string('social_image')->nullable();
            $table->integer('is_available')->default(0);
            $table->string('designation')->nullable();
            $table->string('last_online_time')->nullable();
            $table->integer('slots_for_all_services')->default(0);
            $table->text('known_languages')->nullable();
            $table->text('skills')->nullable();
            $table->text('why_choose_me')->nullable();
            $table->tinyInteger('is_email_verified')->default(0);
            $table->string('language')->nullable();
            $table->date('dob')->nullable();
            $table->tinyInteger('is_phone_verified')->default(0);
            $table->string('stripe_customer_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_types')) {
            Schema::create('provider_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('commission')->default(0);
            $table->integer('status')->default(1);
            $table->string('type')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('handyman_types')) {
            Schema::create('handyman_types', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->double('commission')->default(0);
            $table->integer('status')->default(1);
            $table->string('type')->nullable();
            $table->unsignedBigInteger('created_by')->nullable();
            $table->unsignedBigInteger('updated_by')->nullable();
            $table->unsignedBigInteger('deleted_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('categories')) {
            Schema::create('categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('is_featured')->default(0);
            $table->integer('status')->default(1);
            $table->string('color')->nullable();
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->boolean('seo_enabled')->default(false);
            $table->string('slug')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('sub_categories')) {
            Schema::create('sub_categories', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->integer('is_featured')->default(0);
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('category_id');
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('seo_enabled')->default(false);
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('services')) {
            Schema::create('services', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->string('type')->nullable();
            $table->integer('is_slot')->default(0);
            $table->double('discount')->default(0);
            $table->string('duration')->nullable();
            $table->text('description')->nullable();
            $table->integer('is_featured')->default(0);
            $table->integer('status')->default(1);
            $table->double('price')->default(0);
            $table->integer('added_by')->nullable();
            $table->string('service_request_status')->nullable();
            $table->integer('is_service_request')->default(0);
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->string('service_type')->default('service');
            $table->string('visit_type')->nullable();
            $table->integer('is_enable_advance_payment')->default(0);
            $table->double('advance_payment_amount')->default(0);
            $table->string('meta_title')->nullable();
            $table->text('meta_description')->nullable();
            $table->text('meta_keywords')->nullable();
            $table->string('canonical_url')->nullable();
            $table->string('slug')->nullable();
            $table->boolean('seo_enabled')->default(false);
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('service_addons')) {
            Schema::create('service_addons', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->unsignedBigInteger('service_id');
            $table->double('price')->default(0);
            $table->integer('status')->default(1);
            $table->integer('created_by')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('service_packages')) {
            Schema::create('service_packages', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('description')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->integer('status')->default(1);
            $table->double('price')->default(0);
            $table->dateTime('start_at')->nullable();
            $table->dateTime('end_at')->nullable();
            $table->integer('is_featured')->default(0);
            $table->unsignedBigInteger('category_id')->nullable();
            $table->unsignedBigInteger('subcategory_id')->nullable();
            $table->string('package_type')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('service_zones')) {
            Schema::create('service_zones', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->text('coordinates')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->double('radius')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('service_zone_mappings')) {
            Schema::create('service_zone_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('zone_id');
            });
        }

        if (!Schema::hasTable('category_service_zone')) {
            Schema::create('category_service_zone', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('category_id');
            $table->unsignedBigInteger('service_zone_id');
            });
        }

        if (!Schema::hasTable('provider_address_mappings')) {
            Schema::create('provider_address_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->string('address')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_service_address_mappings')) {
            Schema::create('provider_service_address_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->unsignedBigInteger('provider_address_id');
            });
        }

        if (!Schema::hasTable('provider_zone_mappings')) {
            Schema::create('provider_zone_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('zone_id');
            });
        }

        if (!Schema::hasTable('provider_slot_mappings')) {
            Schema::create('provider_slot_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('day_id');
            $table->time('start_time')->nullable();
            $table->time('end_time')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_documents')) {
            Schema::create('provider_documents', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('document_id');
            $table->string('file')->nullable();
            $table->string('number')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_taxes')) {
            Schema::create('provider_taxes', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('tax_id');
            });
        }

        if (!Schema::hasTable('provider_subscriptions')) {
            Schema::create('provider_subscriptions', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->unsignedBigInteger('plan_id');
            $table->dateTime('start_date')->nullable();
            $table->dateTime('end_date')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('provider_payouts')) {
            Schema::create('provider_payouts', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('provider_id');
            $table->double('amount')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('documents')) {
            Schema::create('documents', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('is_required')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('bookings')) {
            Schema::create('bookings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('post_request_id')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->date('date')->nullable();
            $table->time('start_at')->nullable();
            $table->time('end_at')->nullable();
            $table->double('amount')->default(0);
            $table->double('discount')->default(0);
            $table->double('total_amount')->default(0);
            $table->integer('quantity')->default(1);
            $table->text('description')->nullable();
            $table->unsignedBigInteger('coupon_id')->nullable();
            $table->integer('status')->default(1);
            $table->unsignedBigInteger('payment_id')->nullable();
            $table->text('reason')->nullable();
            $table->text('address')->nullable();
            $table->string('duration_diff')->nullable();
            $table->unsignedBigInteger('booking_address_id')->nullable();
            $table->double('tax')->default(0);
            $table->string('booking_slot')->nullable();
            $table->string('booking_day')->nullable();
            $table->double('advance_paid_amount')->default(0);
            $table->double('final_total_service_price')->default(0);
            $table->double('final_total_tax')->default(0);
            $table->double('final_sub_total')->default(0);
            $table->double('final_discount_amount')->default(0);
            $table->double('final_coupon_discount_amount')->default(0);
            $table->double('cancellation_charge')->default(0);
            $table->double('cancellation_charge_amount')->default(0);
            $table->unsignedBigInteger('zone_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_activities')) {
            Schema::create('booking_activities', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->text('activity')->nullable();
            $table->string('activity_by')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_service_addon_mappings')) {
            Schema::create('booking_service_addon_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('service_addon_id');
            $table->double('price')->default(0);
            });
        }

        if (!Schema::hasTable('booking_handyman_mappings')) {
            Schema::create('booking_handyman_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('handyman_id');
            });
        }

        if (!Schema::hasTable('booking_coupon_mappings')) {
            Schema::create('booking_coupon_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('coupon_id');
            $table->double('discount')->default(0);
            });
        }

        if (!Schema::hasTable('booking_package_mappings')) {
            Schema::create('booking_package_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('service_package_id');
            });
        }

        if (!Schema::hasTable('booking_extra_charges')) {
            Schema::create('booking_extra_charges', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('title')->nullable();
            $table->double('amount')->default(0);
            });
        }

        if (!Schema::hasTable('booking_address_mappings')) {
            Schema::create('booking_address_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->string('address')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            });
        }

        if (!Schema::hasTable('booking_statuses')) {
            Schema::create('booking_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('booking_ratings')) {
            Schema::create('booking_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->double('rating')->default(0);
            $table->text('comment')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('handyman_ratings')) {
            Schema::create('handyman_ratings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('handyman_id');
            $table->unsignedBigInteger('booking_id')->nullable();
            $table->double('rating')->default(0);
            $table->text('comment')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('payments')) {
            Schema::create('payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->unsignedBigInteger('booking_id');
            $table->dateTime('datetime')->nullable();
            $table->double('discount')->default(0);
            $table->double('total_amount')->default(0);
            $table->string('payment_type')->nullable();
            $table->string('txn_id')->nullable();
            $table->string('payment_status')->nullable();
            $table->text('other_transaction_detail')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('payment_histories')) {
            Schema::create('payment_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('payment_id');
            $table->double('amount')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('payment_gateways')) {
            Schema::create('payment_gateways', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->string('payment_environment')->nullable();
            $table->integer('is_active')->default(0);
            $table->text('credentials')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('coupons')) {
            Schema::create('coupons', function (Blueprint $table) {
            $table->id();
            $table->string('code');
            $table->string('discount_type')->nullable();
            $table->double('discount')->default(0);
            $table->dateTime('expire_date')->nullable();
            $table->integer('status')->default(1);
            $table->string('type')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('coupon_service_mappings')) {
            Schema::create('coupon_service_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('coupon_id');
            $table->unsignedBigInteger('service_id');
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('wallets')) {
            Schema::create('wallets', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('title')->nullable();
            $table->double('amount')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('wallet_histories')) {
            Schema::create('wallet_histories', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('wallet_id');
            $table->double('amount')->default(0);
            $table->string('type')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('plans')) {
            Schema::create('plans', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('identifier')->nullable();
            $table->string('playstore_identifier')->nullable();
            $table->string('appstore_identifier')->nullable();
            $table->string('type')->nullable();
            $table->double('amount')->default(0);
            $table->integer('status')->default(1);
            $table->integer('duration')->default(0);
            $table->text('description')->nullable();
            $table->integer('trial_period')->default(0);
            $table->string('plan_type')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('plan_limits')) {
            Schema::create('plan_limits', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('plan_id');
            $table->string('key')->nullable();
            $table->string('value')->nullable();
            });
        }

        if (!Schema::hasTable('static_data')) {
            Schema::create('static_data', function (Blueprint $table) {
            $table->id();
            $table->string('type');
            $table->string('name');
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('taxes')) {
            Schema::create('taxes', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->string('type')->nullable();
            $table->double('value')->default(0);
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('blogs')) {
            Schema::create('blogs', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->double('total_views')->default(0);
            $table->unsignedBigInteger('author_id');
            $table->integer('status')->default(1);
            $table->integer('is_featured')->default(0);
            $table->text('tags')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('help_desk')) {
            Schema::create('help_desk', function (Blueprint $table) {
            $table->id();
            $table->string('subject');
            $table->unsignedBigInteger('employee_id');
            $table->string('email')->nullable();
            $table->string('contact_number')->nullable();
            $table->string('mode')->nullable();
            $table->text('description')->nullable();
            $table->integer('status')->default(0);
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('help_desk_activity_mappings')) {
            Schema::create('help_desk_activity_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('helpdesk_id');
            $table->text('message')->nullable();
            $table->unsignedBigInteger('user_id')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('addresses')) {
            Schema::create('addresses', function (Blueprint $table) {
            $table->id();
            $table->text('address')->nullable();
            $table->string('lat')->nullable();
            $table->string('long')->nullable();
            $table->unsignedBigInteger('user_id');
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('banks')) {
            Schema::create('banks', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('account_holder_name')->nullable();
            $table->string('account_number')->nullable();
            $table->string('bank_name')->nullable();
            $table->string('swift_code')->nullable();
            $table->string('ifsc_code')->nullable();
            $table->string('branch_name')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('sliders')) {
            Schema::create('sliders', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->unsignedBigInteger('type_id')->nullable();
            $table->integer('status')->default(1);
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('settings')) {
            Schema::create('settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('key')->nullable();
            $table->text('value')->nullable();
            });
        }

        if (!Schema::hasTable('frontend_settings')) {
            Schema::create('frontend_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('key')->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('app_settings')) {
            Schema::create('app_settings', function (Blueprint $table) {
            $table->id();
            $table->string('type')->nullable();
            $table->string('key')->nullable();
            $table->text('value')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('translations')) {
            Schema::create('translations', function (Blueprint $table) {
            $table->id();
            $table->string('locale');
            $table->string('attribute');
            $table->text('value');
            $table->morphs('translatable');
            });
        }

        if (!Schema::hasTable('notifications')) {
            Schema::create('notifications', function (Blueprint $table) {
            $table->uuid('id')->primary();
            $table->string('type');
            $table->morphs('notifiable');
            $table->text('data');
            $table->timestamp('read_at')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('notification_templates')) {
            Schema::create('notification_templates', function (Blueprint $table) {
            $table->id();
            $table->string('title');
            $table->text('description')->nullable();
            $table->string('type')->nullable();
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('mail_templates')) {
            Schema::create('mail_templates', function (Blueprint $table) {
            $table->id();
            $table->string('name')->nullable();
            $table->string('slug')->nullable();
            $table->string('from')->nullable();
            $table->string('subject')->nullable();
            $table->text('body')->nullable();
            $table->string('status')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('mail_template_content_mappings')) {
            Schema::create('mail_template_content_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('mail_template_id');
            $table->string('language')->nullable();
            $table->text('content')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('promotional_banners')) {
            Schema::create('promotional_banners', function (Blueprint $table) {
            $table->id();
            $table->string('title')->nullable();
            $table->text('description')->nullable();
            $table->string('banner_type')->nullable();
            $table->string('banner_redirect_url')->nullable();
            $table->integer('is_requested_banner')->default(0);
            $table->integer('status')->default(1);
            $table->text('reject_reason')->nullable();
            $table->integer('duration')->nullable();
            $table->double('charges')->default(0);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->double('total_amount')->default(0);
            $table->string('payment_method')->nullable();
            $table->string('payment_status')->nullable();
            $table->unsignedBigInteger('service_id')->nullable();
            $table->unsignedBigInteger('provider_id')->nullable();
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('banner_payments')) {
            Schema::create('banner_payments', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('promotional_banner_id');
            $table->double('amount')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('service_faqs')) {
            Schema::create('service_faqs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('question');
            $table->text('answer')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('service_proofs')) {
            Schema::create('service_proofs', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_id');
            $table->string('name')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('package_service_mappings')) {
            Schema::create('package_service_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('service_package_id');
            $table->unsignedBigInteger('service_id');
            });
        }

        if (!Schema::hasTable('post_request_statuses')) {
            Schema::create('post_request_statuses', function (Blueprint $table) {
            $table->id();
            $table->string('name');
            $table->integer('status')->default(1);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('post_job_requests')) {
            Schema::create('post_job_requests', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('customer_id');
            $table->text('description')->nullable();
            $table->date('date')->nullable();
            $table->time('time')->nullable();
            $table->double('budget')->default(0);
            $table->unsignedBigInteger('status_id')->nullable();
            $table->text('address')->nullable();
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('post_job_service_mappings')) {
            Schema::create('post_job_service_mappings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_job_request_id');
            $table->unsignedBigInteger('service_id');
            });
        }

        if (!Schema::hasTable('post_job_bids')) {
            Schema::create('post_job_bids', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('post_job_request_id');
            $table->unsignedBigInteger('provider_id');
            $table->double('amount')->default(0);
            $table->text('description')->nullable();
            $table->integer('status')->default(0);
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_favourite_services')) {
            Schema::create('user_favourite_services', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('service_id');
            $table->softDeletes();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('user_favourite_providers')) {
            Schema::create('user_favourite_providers', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->unsignedBigInteger('provider_id');
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('commission_earnings')) {
            Schema::create('commission_earnings', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('user_id');
            $table->double('amount')->default(0);
            $table->string('type')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('live_locations')) {
            Schema::create('live_locations', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('booking_id');
            $table->unsignedBigInteger('handyman_id');
            $table->double('latitude')->nullable();
            $table->double('longitude')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('withdraw_money')) {
            Schema::create('withdraw_money', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->double('amount')->default(0);
            $table->string('status')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('app_downloads')) {
            Schema::create('app_downloads', function (Blueprint $table) {
            $table->id();
            $table->unsignedBigInteger('user_id');
            $table->string('platform')->nullable();
            $table->timestamps();
            });
        }

        if (!Schema::hasTable('personal_access_tokens')) {
            Schema::create('personal_access_tokens', function (Blueprint $table) {
            $table->id();
            $table->morphs('tokenable');
            $table->string('name');
            $table->string('token', 64)->unique();
            $table->text('abilities')->nullable();
            $table->timestamp('last_used_at')->nullable();
            $table->timestamp('expires_at')->nullable();
            $table->timestamps();
            });
        }
    }

        public function down(): void
    {
        if (!app()->environment('testing')) {
            return;
        }

        $tables = [
            'personal_access_tokens', 'app_downloads', 'withdraw_money', 'live_locations',
            'commission_earnings', 'user_favourite_providers', 'user_favourite_services',
            'post_job_bids', 'post_job_service_mappings', 'post_job_requests',
            'post_request_statuses', 'service_proofs', 'service_faqs',
            'banner_payments', 'promotional_banners', 'mail_template_content_mappings',
            'mail_templates', 'notification_templates', 'notifications', 'translations',
            'app_settings', 'frontend_settings', 'settings', 'sliders', 'banks', 'addresses',
            'help_desk_activity_mappings', 'help_desk', 'blogs', 'taxes', 'static_data',
            'plan_limits', 'plans', 'wallet_histories', 'wallets', 'coupon_service_mappings',
            'coupons', 'payment_gateways', 'payment_histories', 'payments',
            'handyman_ratings', 'booking_ratings', 'booking_statuses',
            'booking_address_mappings', 'booking_extra_charges', 'booking_package_mappings',
            'booking_coupon_mappings', 'booking_handyman_mappings',
            'booking_service_addon_mappings', 'booking_activities', 'bookings',
            'documents', 'provider_payouts', 'provider_subscriptions', 'provider_taxes',
            'provider_documents', 'provider_slot_mappings', 'provider_zone_mappings',
            'provider_service_address_mappings', 'provider_address_mappings',
            'category_service_zone', 'service_zone_mappings', 'service_zones',
            'service_packages', 'service_addons', 'services', 'sub_categories', 'categories',
            'handyman_types', 'provider_types', 'users', 'cities', 'states', 'countries',
            'model_has_permissions', 'model_has_roles', 'role_has_permissions',
            'permissions', 'roles',
        ];
        foreach ($tables as $table) {
            Schema::dropIfExists($table);
        }
    }
};
