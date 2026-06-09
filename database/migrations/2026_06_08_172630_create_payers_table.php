<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('payers', function (Blueprint $table) {
            $table->id();

            // Internal
            $table->foreignId('user_id')->constrained()->nullOnDelete();
            $table->uuid('uuid')->unique();

            // Type
            $table->enum('file_type', ['Individual', 'Business'])->comment('Type: Individual or Business');

            $table->string('name', 100)->nullable()->comment('Individual: Full Name / Business: Business Name');
            // ── INDIVIDUAL fields ──────────────────────────────────────
            $table->string('first_name', 100)->nullable()->comment('Individual: First Name');
            $table->string('middle_name', 100)->nullable()->comment('Individual: Middle Name');
            $table->string('last_name', 100)->nullable()->comment('Individual: Last Name / Business Name');
            $table->enum('suffix', ['Jr', 'Sr', '2nd', 'C3rd', 'II','III', 'IV', 'V', 'VI'])->nullable()->comment('Individual: Suffix e.g. Jr, Sr, II');

            // ── ID Number ─────────────────────────────────────────
            $table->enum('id_type', ['SSN', 'EIN'])->nullable()->comment('ID Type: SSN for Individual, EIN for Business');
            $table->string('id_number', 11)->nullable()->comment('Individual: SSN (###-##-####) / Business: EIN (##-#######)');

            // ── Shared basic info ──────────────────────────────────────
            $table->string('email', 255)->nullable()->comment('Email Address');
            $table->string('phone_number', 20)->nullable()->comment('Phone Number');
            $table->string('disregarded_entity', 200)->nullable()->comment('Disregarded Entity');

            // ── Address ────────────────────────────────────────────────
            $table->string('address_one', 255)->nullable()->comment('Address Line 1');
            $table->string('address_two', 255)->nullable()->comment('Address Line 2 (Optional)');
            $table->string('city', 100)->nullable()->comment('City');
            $table->char('state', 2)->nullable()->comment('State: 2-letter code');
            $table->string('zip_code', 10)->nullable()->comment('ZipCode: supports ZIP+4');
            $table->char('country', 2)->nullable()->default('US')->comment('Country: ISO 3166-1 alpha-2');
            $table->boolean('is_foreign_address')->default(false)->comment('Click here for foreign address');

            // ── Optional Information ───────────────────────────────────
            $table->string('withholding_tax_state_id', 100)->nullable()->comment('Withholding/Tax State Id');
            $table->string('client_payer_id', 100)->nullable()->index()->comment('Client Payer Id');
            $table->string('group_id', 100)->nullable()->index()->comment('Group Id');
            $table->boolean('is_last_filing')->default(false)->comment('Is this the last filing for the payer?');

            // ── tax1099 sync ───────────────────────────────────────────
            $table->string('payer_detail_id', 100)->nullable()->unique()->comment('PayerDetailId returned by tax1099 API');
            $table->string('tin_status', 50)->nullable()->comment('TinStatus from tax1099');
            $table->boolean('is_tin_check')->default(false)->comment('IsTinCheck');
            $table->boolean('un_mask_recipient_tin')->default(false)->comment('UnMaskRecipientTin');
            $table->string('trade_name', 200)->nullable()->comment('TradeName');

            // ── Status & audit ─────────────────────────────────────────
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            // Indexes
            $table->index(['user_id', 'is_active']);
            $table->index(['file_type', 'is_active']);
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('payers');
    }
};