<?php

namespace App\Modules\Auth\DTOs;

final readonly class MfaSetupDTO
{
    public function __construct(
        public string $secret,
        public string $qrCodeUrl,
        public string $manualEntryKey,
    ) {}

    public function toArray(): array
    {
        return [
            'secret'           => $this->secret,
            'qr_code_url'      => $this->qrCodeUrl,
            'manual_entry_key' => $this->manualEntryKey,
        ];
    }
}
