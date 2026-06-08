<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\Contracts\AuthRepositoryContract;
use App\Modules\Auth\DTOs\MfaSetupDTO;
use PragmaRX\Google2FA\Google2FA;

class SetupMfaAction
{
    public function __construct(
        private readonly Google2FA              $google2fa,
        private readonly AuthRepositoryContract $authRepository,
    ) {}

    public function execute(User $user): MfaSetupDTO
    {
        $secret    = $this->google2fa->generateSecretKey(32);
        $qrCodeUrl = $this->google2fa->getQRCodeUrl(
            config('app.name'),
            $user->email,
            $secret
        );

        // Store encrypted — never plain text
        $this->authRepository->enableMfa($user, encrypt($secret));

        return new MfaSetupDTO(
            secret:         $secret,
            qrCodeUrl:      $qrCodeUrl,
            manualEntryKey: chunk_split($secret, 4, ' '),
        );
    }
}
