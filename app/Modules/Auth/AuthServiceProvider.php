<?php

namespace App\Modules\Auth;

use App\Modules\Auth\Actions\IssueTokenAction;
use App\Modules\Auth\Actions\SetupMfaAction;
use App\Modules\Auth\Actions\ValidateCredentialsAction;
use App\Modules\Auth\Actions\VerifyMfaAction;
use App\Modules\Auth\Contracts\AuthRepositoryContract;
use App\Modules\Auth\Repositories\AuthRepository;
use App\Modules\Auth\Services\AuthService;
use Illuminate\Support\ServiceProvider;
use PragmaRX\Google2FA\Google2FA;

class AuthServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        // Bind repository contract to concrete implementation
        $this->app->bind(AuthRepositoryContract::class, AuthRepository::class);

        // Bind Google2FA as singleton (stateless, safe to share)
        $this->app->singleton(Google2FA::class, fn() => new Google2FA());

        // Bind Actions
        $this->app->bind(ValidateCredentialsAction::class, fn($app) =>
            new ValidateCredentialsAction(
                authRepository: $app->make(AuthRepositoryContract::class),
            )
        );

        $this->app->bind(VerifyMfaAction::class, fn($app) =>
            new VerifyMfaAction(
                google2fa: $app->make(Google2FA::class),
            )
        );

        $this->app->bind(SetupMfaAction::class, fn($app) =>
            new SetupMfaAction(
                google2fa:      $app->make(Google2FA::class),
                authRepository: $app->make(AuthRepositoryContract::class),
            )
        );

        // Bind AuthService with all dependencies
        $this->app->bind(AuthService::class, fn($app) =>
            new AuthService(
                validateCredentials: $app->make(ValidateCredentialsAction::class),
                issueToken:          $app->make(IssueTokenAction::class),
                verifyMfa:           $app->make(VerifyMfaAction::class),
                setupMfa:            $app->make(SetupMfaAction::class),
                authRepository:      $app->make(AuthRepositoryContract::class),
            )
        );
    }

    public function boot(): void
    {
        // Load module routes
        // $this->loadRoutesFrom(__DIR__ . '/../../routes/auth.php');
    }
}
