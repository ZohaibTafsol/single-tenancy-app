<?php

namespace App\Modules\Recipient\DTOs;

class RecipientDTO
{
    public function __construct(
        public ?int $id,
        public ?int $payer_id,
        public ?string $first_name,
        public ?string $middle_name,
        public ?string $suffix,
        public ?string $business_name,
        public ?string $last_name,
        public ?string $attention_to,
        public ?string $ssn,
        public ?string $ein,
        public ?string $email,
        public ?string $phone,
        public ?string $address1,
        public ?string $address2,
        public ?string $city,
        public ?string $state,
        public ?string $zipcode,
        public ?string $country,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['id'] ?? null,
            $data['payer_id'] ?? null,
            $data['first_name'] ?? null,
            $data['middle_name'] ?? null,
            $data['suffix'] ?? null,
            $data['business_name'] ?? null,
            $data['last_name'] ?? null,
            $data['attention_to'] ?? null,
            $data['ssn'] ?? null,
            $data['ein'] ?? null,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address1'] ?? null,
            $data['address2'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['zipcode'] ?? null,
            $data['country'] ?? null,
        );
    }
}