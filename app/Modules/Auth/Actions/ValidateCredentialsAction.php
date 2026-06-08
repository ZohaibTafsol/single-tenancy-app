<?php

namespace App\Modules\Auth\Actions;

use App\Models\User;
use App\Modules\Auth\Contracts\AuthRepositoryContract;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\Exceptions\AccountLockedException;
use App\Modules\Auth\Exceptions\InvalidCredentialsException;
use Illuminate\Support\Facades\Hash;

class ValidateCredentialsAction
{
    private const MAX_ATTEMPTS  = 10;
    private const LOCKOUT_MINS  = 30;

    public function __construct(
        private readonly AuthRepositoryContract $authRepository,
    ) {}

    public function execute(LoginDTO $dto): User
    {
        $user = $this->authRepository->findByEmail($dto->email);

        // Check lock first — even before confirming user exists
        // (avoids timing attack that reveals valid emails)
        if ($user && $user->isLocked()) {
            throw new AccountLockedException($user->locked_until);
        }

        if (! $user || ! Hash::check($dto->password, $user->password)) {
            $this->handleFailedAttempt($user);

            throw new InvalidCredentialsException();
        }

        // Successful — reset counter
        $this->authRepository->resetFailedAttempts($user);

        return $user;
    }

    private function handleFailedAttempt(?User $user): void
    {
        if (! $user) {
            return;
        }

        $this->authRepository->incrementFailedAttempts($user);
        $user->refresh();

        if ($user->failed_login_attempts >= self::MAX_ATTEMPTS) {
            $this->authRepository->lockAccount($user, self::LOCKOUT_MINS);
        }
    }
}
