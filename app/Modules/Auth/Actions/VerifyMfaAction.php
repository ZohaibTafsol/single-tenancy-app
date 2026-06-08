<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\DTOs\MfaVerifyDTO;
use App\Modules\Auth\Exceptions\InvalidMfaCodeException;
use App\Modules\Auth\Exceptions\InvalidTokenScopeException;
use PragmaRX\Google2FA\Google2FA;

class VerifyMfaAction
{
    private const ALLOWED_CLOCK_DRIFT = 2; // windows (±60s each)

    public function __construct(
        private readonly Google2FA $google2fa,
    ) {}

    public function execute(User $user, MfaVerifyDTO $dto): void
    {
        // Guard: only mfa:pending tokens can hit this action
        if (! $user->currentAccessToken()->can('mfa:pending')) {
            throw new InvalidTokenScopeException('mfa:pending token required.');
        }

        $valid = $this->google2fa->verifyKey(
            decrypt($user->google2fa_secret),
            $dto->otp,
            self::ALLOWED_CLOCK_DRIFT
        );

        if (! $valid) {
            throw new InvalidMfaCodeException();
        }

        // Consume the temp token — it's single use
        $user->currentAccessToken()->delete();
    }
}
