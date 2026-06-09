<?php

namespace App\Modules\Payer\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Traits\HasUuid;

class Payer extends Model
{
    use HasFactory, HasUuid;

    protected $fillable = [
        'user_id',
        'file_type',
        'name',
        'first_name',
        'middle_name',
        'last_name',
        'suffix',
        'id_type',
        'id_number',
        'address_one',
        'address_two',
        'city',
        'state',
        'zip_code',
        'country',
        'is_foreign_address',
        'email',
        'phone_number',
        'disregarded_entity',
        'withholding_tax_state_id',
        'client_payer_id',
        'group_id',
        'is_last_filing',
        'payer_detail_id',
        'tin_status',
        'is_tin_check',
        'un_mask_recipient_tin',
        'trade_name',
        'is_active',
    ];
}