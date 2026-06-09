<?php

namespace App\Modules\Payer\DTOs;

class PayerDTO
{
    public function __construct(
        public ?string $file_type,
        public ?string $name,

        // Individual
        public ?string $first_name,
        public ?string $middle_name,
        public ?string $last_name,
        public ?string $suffix,

        // ID
        public ?string $id_type,
        public ?string $id_number,

        // Contact
        public ?string $email,
        public ?string $phone_number,
        public ?string $disregarded_entity,

        // Address
        public ?string $address_one,
        public ?string $address_two,
        public ?string $city,
        public ?string $state,
        public ?string $zip_code,
        public ?string $country,
        public ?bool $is_foreign_address,

        // Optional business/meta
        public ?string $withholding_tax_state_id,
        public ?string $client_payer_id,
        public ?string $group_id,
        public ?bool $is_last_filing,

        // Tax1099 sync
        public ?string $payer_detail_id,
        public ?string $tin_status,
        public ?bool $is_tin_check,
        public ?bool $un_mask_recipient_tin,
        public ?string $trade_name,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['file_type'],
            $data['name'] ?? null,

            // Individual
            $data['first_name'] ?? null,
            $data['middle_name'] ?? null,
            $data['last_name'] ?? null,
            $data['suffix'] ?? null,

            // ID
            $data['id_type'] ?? null,
            $data['id_number'] ?? null,

            // Contact
            $data['email'] ?? null,
            $data['phone_number'] ?? null,
            $data['disregarded_entity'] ?? null,

            // Address
            $data['address_one'],
            $data['address_two'] ?? "",
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['zip_code'] ?? null,
            $data['country'] ?? null,
            $data['is_foreign_address'] ?? false,

            // Optional
            $data['withholding_tax_state_id'] ?? null,
            $data['client_payer_id'] ?? null,
            $data['group_id'] ?? null,
            $data['is_last_filing'] ?? false,

            // Tax1099
            $data['payer_detail_id'] ?? null,
            $data['tin_status'] ?? null,
            $data['is_tin_check'] ?? false,
            $data['un_mask_recipient_tin'] ?? false,
            $data['trade_name'] ?? null,
        );
    }

    public function toArray(): array
    {
        return [
            'file_type' => $this->file_type,
            'name' => $this->file_type === 'Individual' ? $this->first_name . ' ' . $this->middle_name . ' ' . $this->last_name : $this->last_name, // Use 'name' for Business, 'last_name' for Individual
            'first_name' => $this->first_name,
            'middle_name' => $this->middle_name,
            'last_name' => $this->last_name,
            'suffix' => $this->suffix,
            'id_type' => $this->id_type,
            'id_number' => $this->id_number,
            'email' => $this->email,
            'phone_number' => $this->phone_number,
            'disregarded_entity' => $this->disregarded_entity,
            'address_one' => $this->address_one,
            'address_two' => $this->address_two,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zip_code,
            'country' => $this->country,
            'is_foreign_address' => $this->is_foreign_address,
            'withholding_tax_state_id' => $this->withholding_tax_state_id,
            'client_payer_id' => $this->client_payer_id,
            'group_id' => $this->group_id,
            'is_last_filing' => $this->is_last_filing,
            'payer_detail_id' => $this->payer_detail_id,
            'tin_status' => $this->tin_status,
            'is_tin_check' => $this->is_tin_check,
            'un_mask_recipient_tin' => $this->un_mask_recipient_tin,
            'trade_name' => $this->trade_name,
        ];
    }
}