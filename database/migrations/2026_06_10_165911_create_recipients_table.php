<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('recipients', function (Blueprint $table) {
            $table->id();
            $table->uuid('uuid')->unique();

            // ── Internal ───────────────────────────────────────────────
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('payer_id')->constrained()->cascadeOnDelete()->comment('The payer this recipient belongs to');

            // ── Type ──────────────────────────────────────────────────
            $table->enum('file_type', ['Individual', 'Business'])->default('Individual')->comment('Type: Individual or Business');

            // ── W-8 / W-9 Request ─────────────────────────────────────
            $table->boolean('w8_request')->default(false)->comment('W-8 Request');
            $table->boolean('w9_request')->default(false)->comment('W-9 Request');

            // ── Basic Information ──────────────────────────────────────
            $table->string('name', 200)->comment('Full Name (Individual) or Business Name (Business)');

            // Individual fields
            $table->string('first_name', 100)->nullable()->comment('Individual: First Name');
            $table->string('middle_name', 100)->nullable()->comment('Individual: Middle Name');
            $table->string('last_name', 100)->nullable()->comment('Individual: Last Name');
            $table->enum('suffix', ['Jr', 'Sr', '2nd', '3rd', 'II', 'III', 'IV', 'V', 'VI'])->nullable()->comment('Individual: Suffix e.g. Jr, Sr, II');

            // ── TIN (Tax Identification Number) ───────────────────────
            $table->enum('tin_type', ['SSN', 'EIN', 'ITIN', 'ATIN'])->default('SSN')->nullable()->comment('TIN Type: SSN/ITIN/ATIN for Individual, EIN for Business');
            $table->string('tin', 11)->nullable()->comment('Individual: SSN (###-##-####) / Business: EIN (##-#######)');
            $table->boolean('tin_not_provided')->default(false)->comment('Checked when recipient has not provided a TIN');

            // ── Contact Information ────────────────────────────────────
            $table->string('attention_to', 200)->nullable()->comment('Attention To / Care Of');
            $table->string('email', 255)->nullable()->comment('Email Address');
            $table->string('phone_number', 20)->nullable()->comment('Phone Number');

            // ── Address ────────────────────────────────────────────────
            $table->string('address_one', 255)->nullable()->comment('Address Line 1');
            $table->string('address_two', 255)->nullable()->comment('Address Line 2 (Optional)');
            $table->string('city', 100)->nullable()->comment('City');
            $table->string('state', 100)->nullable()->comment('State, province, region, or administrative area');
            $table->string('zip_code', 10)->nullable()->comment('ZIP Code: supports ZIP+4 format (e.g. 12345-6789)');
            $table->string('country', 100)->default('US')->comment('Country name or ISO 3166-1 alpha-2 country code');
            $table->boolean('is_foreign_address')->default(false)->comment('Flag for foreign (non-US) address');

            // ── Optional / Client Fields ───────────────────────────────
            $table->string('client_recipient_id', 100)->nullable()->index()->comment('Client-side Recipient ID for cross-referencing');
            $table->string('email_language', 10)->default('en')->comment('Language for recipient emails e.g. en, es, fr');

            // ── tax1099 API Sync ───────────────────────────────────────
            $table->string('recipient_detail_id', 100)->nullable()->unique()->comment('RecipientDetailId returned by tax1099 API');
            $table->string('tin_status', 50)->nullable()->comment('TIN verification status from tax1099 (e.g. Match, NoMatch, Pending)');
            $table->boolean('is_tin_check')->default(false)->comment('Whether TIN check was requested via tax1099');
            $table->boolean('un_mask_recipient_tin')->default(false)->comment('Whether to unmask TIN in tax1099 portal');
            $table->string('account_number', 100)->nullable()->comment('Account number assigned by filer (Box for 1099 forms)');
            $table->string('second_tin_notice', 10)->nullable()->comment('2nd TIN Notice from IRS — checked on 1099 form');

            // ── 1099 Form-specific flags ───────────────────────────────
            $table->boolean('fatca_filing_requirement')->default(false)->comment('FATCA filing requirement checkbox (appears on some 1099 forms)');
            $table->boolean('is_last_filing')->default(false)->comment('Mark if this is the last year for this recipient');

            // ── Status & Audit ─────────────────────────────────────────
            $table->boolean('is_active')->default(true);
            $table->softDeletes();
            $table->timestamps();

            // ── Indexes ────────────────────────────────────────────────
            $table->index(['user_id', 'payer_id']);
            $table->index(['user_id', 'is_active']);
            $table->index(['file_type', 'is_active']);
            $table->index('email');
            $table->index('tin');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('recipients');
    }
};