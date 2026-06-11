<?php

namespace App\Modules\Recipient\DTOs;

class RecipientDTO
{
    public function __construct(
        public ?string $payerUuid,
        public ?string $fileType,
        public ?bool $w8Request,
        public ?bool $w9Request,
        public ?string $firstName,
        public ?string $middleName,
        public ?string $lastName,
        public ?string $suffix,
        public ?string $attentionTo,
        public ?string $tinType,
        public ?string $tin,
        public ?bool $tinNotProvided,
        public ?string $email,
        public ?string $phoneNumber,
        public ?string $addressOne,
        public ?string $addressTwo,
        public ?string $city,
        public ?string $state,
        public ?string $zipCode,
        public ?string $country,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            $data['payer_uuid'] ?? null,
            $data['file_type'] ?? null,
            $data['w8_request'] ?? false,
            $data['w9_request'] ?? false,
            $data['first_name'] ?? null,
            $data['middle_name'] ?? null,
            $data['last_name'] ?? null,
            $data['suffix'] ?? null,
            $data['attention_to'] ?? null,
            $data['tin_type'] ?? 'SSN',
            $data['recipient_tin'] ?? null,
            $data['tin_not_provided'] ?? false,
            $data['email'] ?? null,
            $data['phone'] ?? null,
            $data['address_one'] ?? null,
            $data['address_two'] ?? null,
            $data['city'] ?? null,
            $data['state'] ?? null,
            $data['zip_code'] ?? null,
            $data['country'] ?? null,
        );
    }
    public function toArray(): array
    {
        return [
            'payer_uuid' => $this->payerUuid,
            'file_type' => $this->fileType,
            'w8_request' => $this->w8Request,
            'w9_request' => $this->w9Request,
            'name' => $this->fileType === 'Individual' ? $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName : $this->lastName,
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'suffix' => $this->suffix,
            'attention_to' => $this->attentionTo,
            'tin_type' => $this->tinType,
            'tin' => $this->tin,
            'tin_not_provided' => $this->tinNotProvided,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'address_one' => $this->addressOne,
            'address_two' => $this->addressTwo,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
            'country' => $this->country,
        ];
    }
}