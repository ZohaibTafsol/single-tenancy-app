<?php

namespace App\Modules\Payer\DTOs;

class PayerDTO
{
    public function __construct(
        public ?string $fileType,
        public ?string $name,
        public int $userId,

        // Individual
        public ?string $firstName,
        public ?string $middleName,
        public ?string $lastName,
        public ?string $suffix,

        // ID
        public ?string $idType,
        public ?string $idNumber,

        // Contact
        public ?string $email,
        public ?string $phoneNumber,
        public ?string $disregardedEntity,

        // Address
        public ?string $addressOne,
        public ?string $addressTwo,
        public ?string $city,
        public ?string $state,
        public ?string $zipCode,
        public ?string $country,
        public ?bool $isForeignAddress,

        // Optional business/meta
        public ?string $withholdingTaxStateId,
        public ?string $clientPayerId,
        public ?string $groupId,
        public ?bool $isLastFiling,

        // Tax1099 sync
        public ?string $payerDetailId,
        public ?string $tinStatus,
        public ?bool $isTinCheck,
        public ?bool $unMaskRecipientTin,
        public ?string $tradeName,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(

            $data['file_type'],
            $data['name'] ?? null,
            $data['user_id'],

            // Individual
            $data['first_name'] ?? null,
            $data['middle_name'] ?? null,
            $data['last_name'],
            $data['suffix'] ?? null,

            // ID
            $data['id_type'] ?? null,
            $data['id_number'],

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
            'user_id' => $this->userId,
            'file_type' => $this->fileType,
            'name' => $this->fileType === 'Individual' ? $this->firstName . ' ' . $this->middleName . ' ' . $this->lastName : $this->lastName, // Use 'name' for Business, 'last_name' for Individual
            'first_name' => $this->firstName,
            'middle_name' => $this->middleName,
            'last_name' => $this->lastName,
            'suffix' => $this->suffix,
            'id_type' => $this->idType,
            'id_number' => $this->idNumber,
            'email' => $this->email,
            'phone_number' => $this->phoneNumber,
            'disregarded_entity' => $this->disregardedEntity,
            'address_one' => $this->addressOne,
            'address_two' => $this->addressTwo,
            'city' => $this->city,
            'state' => $this->state,
            'zip_code' => $this->zipCode,
            'country' => $this->country,
            'is_foreign_address' => $this->isForeignAddress,
            'withholding_tax_state_id' => $this->withholdingTaxStateId,
            'client_payer_id' => $this->clientPayerId,
            'group_id' => $this->groupId,
            'is_last_filing' => $this->isLastFiling,
            'payer_detail_id' => $this->payerDetailId,
            'tin_status' => $this->tinStatus,
            'is_tin_check' => $this->isTinCheck,
            'un_mask_recipient_tin' => $this->unMaskRecipientTin,
            'trade_name' => $this->tradeName,
        ];
    }
}