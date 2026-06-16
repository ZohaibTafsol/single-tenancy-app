<?php

namespace App\Modules\Tenant\DTOs;

class DomainDTO
{
    public function __construct(
        public readonly string $domainName,
    ) {}

    public static function fromRequest(array $data): self
    {
        return new self(
            domainName: $data['domain_name'],
        );
    }

    public function toArray(): array
    {
        return [
            'domain_name' => $this->domainName,
        ];
    }
}
