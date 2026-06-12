<?php

namespace App\Modules\Recipient\Models;

use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Recipient extends Model
{
    use HasUuid;

    protected $fillable = [

        // Relations
        'user_id',
        'payer_id',

        // Type
        'file_type',

        // W-8 / W-9
        'w8_request',
        'w9_request',

        // Basic Information
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',

        // TIN
        'tin_type',
        'tin',
        'tin_not_provided',

        // Contact
        'attention_to',
        'email',
        'phone_number',

        // Address
        'address_one',
        'address_two',
        'city',
        'state',
        'zip_code',
        'country',
        'is_foreign_address',

        // Client Fields
        'client_recipient_id',
        'email_language',

        // tax1099 Sync
        'recipient_detail_id',
        'tin_status',
        'is_tin_check',
        'un_mask_recipient_tin',
        'account_number',
        'second_tin_notice',

        // Form Flags
        'fatca_filing_requirement',
        'is_last_filing',

        // Status
        'is_active',
    ];

    protected $casts = [
        'w8_request' => 'boolean',
        'w9_request' => 'boolean',
        'tin_not_provided' => 'boolean',
        'is_foreign_address' => 'boolean',
        'is_tin_check' => 'boolean',
        'un_mask_recipient_tin' => 'boolean',
        'fatca_filing_requirement' => 'boolean',
        'is_last_filing' => 'boolean',
        'is_active' => 'boolean',
        'deleted_at' => 'datetime',
    ];
}
