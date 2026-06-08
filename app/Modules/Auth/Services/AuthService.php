<?php

namespace App\Modules\Auth\Services;

use App\Models\User;
use App\Modules\Auth\Actions\IssueTokenAction;
use App\Modules\Auth\Actions\SetupMfaAction;
use App\Modules\Auth\Actions\ValidateCredentialsAction;
use App\Modules\Auth\Actions\VerifyMfaAction;
use App\Modules\Auth\Contracts\AuthRepositoryContract;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\MfaSetupDTO;
use App\Modules\Auth\DTOs\MfaVerifyDTO;
use App\Modules\Auth\DTOs\TokenDTO;
use App\Modules\Auth\Exceptions\InvalidTokenScopeException;
use Illuminate\Support\Facades\Hash;

class AuthService
{
    public function __construct(
        private readonly ValidateCredentialsAction $validateCredentials,
        private readonly IssueTokenAction          $issueToken,
        private readonly VerifyMfaAction           $verifyMfa,
        private readonly SetupMfaAction            $setupMfa,
        private readonly AuthRepositoryContract    $authRepository,
    ) {}

    // ─── Login: Step 1 ───────────────────────────────────────────────
    public function login(LoginDTO $dto): TokenDTO
    {
        $user = $this->validateCredentials->execute($dto);

        // MFA not enabled → issue full token immediately
        if (! $user->mfa_enabled) {
            return $this->issueToken->executeFullAccess($user);
        }

        // MFA enabled → issue short-lived pending token
        return $this->issueToken->executeMfaPending($user);
    }

    // ─── Login: Step 2 — MFA verification ────────────────────────────
    public function verifyMfa(User $user, MfaVerifyDTO $dto): TokenDTO
    {
        $this->verifyMfa->execute($user, $dto);

        return $this->issueToken->executeFullAccess($user);
    }

    // ─── Refresh token ────────────────────────────────────────────────
    public function refresh(User $user): TokenDTO
    {
        if (! $user->currentAccessToken()->can('refresh')) {
            throw new InvalidTokenScopeException('Refresh token required.');
        }

        return $this->issueToken->executeRefresh($user);
    }

    // ─── Logout current device ────────────────────────────────────────
    public function logout(User $user): void
    {
        $this->authRepository->revokeToken($user, $user->currentAccessToken()->id);
    }

    // ─── Logout all devices ───────────────────────────────────────────
    public function logoutAll(User $user): void
    {
        $this->authRepository->revokeAllTokens($user);
    }

    // ─── Setup MFA ────────────────────────────────────────────────────
    public function setupMfa(User $user): MfaSetupDTO
    {
        return $this->setupMfa->execute($user);
    }

    // ─── Confirm MFA after scanning QR ───────────────────────────────
    public function confirmMfa(User $user, string $otp): void
    {
        $dto = new MfaVerifyDTO(
            otp:       $otp,
            userId:    $user->id,
            ipAddress: request()->ip(),
        );

        // Re-use verify action (without consuming a token this time)
        app(VerifyMfaAction::class)->execute($user, $dto);
    }

    // ─── Disable MFA ──────────────────────────────────────────────────
    public function disableMfa(User $user, string $password): void
    {
        if (! Hash::check($password, $user->password)) {
            throw new \App\Modules\Auth\Exceptions\InvalidCredentialsException('Wrong password.');
        }

        $this->authRepository->disableMfa($user);
    }
}
