<?php

namespace App\Modules\Auth\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Auth\DTOs\LoginDTO;
use App\Modules\Auth\DTOs\MfaVerifyDTO;
use App\Modules\Auth\Exceptions\AccountLockedException;
use App\Modules\Auth\Exceptions\InvalidCredentialsException;
use App\Modules\Auth\Exceptions\InvalidMfaCodeException;
use App\Modules\Auth\Exceptions\InvalidTokenScopeException;
use App\Modules\Auth\Http\Requests\DisableMfaRequest;
use App\Modules\Auth\Http\Requests\LoginRequest;
use App\Modules\Auth\Http\Requests\MfaVerifyRequest;
use App\Modules\Auth\Services\AuthService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class AuthController extends Controller
{
    use ApiResponse;

    public function __construct(
        private readonly AuthService $authService,
    ) {}

    // POST /api/auth/login
    public function login(LoginRequest $request): JsonResponse
    {
        try {
            $dto    = LoginDTO::fromRequest(
                data:      $request->validated(),
                ip:        $request->ip(),
                userAgent: $request->userAgent() ?? '',
            );

            $result = $this->authService->login($dto);

            return $this->success($result->toArray(), 'Login successful.');

        } catch (AccountLockedException $e) {
            return $this->error($e->getMessage(), 423);

        } catch (InvalidCredentialsException $e) {
            return $this->unauthorized($e->getMessage());
        }
    }

    // POST /api/auth/mfa/verify  (Bearer: temp_token)
    public function verifyMfa(MfaVerifyRequest $request): JsonResponse
    {
        try {
            $dto    = MfaVerifyDTO::fromRequest(
                data:   $request->validated(),
                userId: $request->user()->id,
                ip:     $request->ip(),
            );

            $result = $this->authService->verifyMfa($request->user(), $dto);

            return $this->success($result->toArray(), 'MFA verified successfully.');

        } catch (InvalidTokenScopeException $e) {
            return $this->forbidden($e->getMessage());

        } catch (InvalidMfaCodeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    // POST /api/auth/refresh  (Bearer: refresh_token)
    public function refresh(Request $request): JsonResponse
    {
        try {
            $result = $this->authService->refresh($request->user());

            return $this->success($result->toArray(), 'Token refreshed.');

        } catch (InvalidTokenScopeException $e) {
            return $this->forbidden($e->getMessage());
        }
    }

    // POST /api/auth/logout
    public function logout(Request $request): JsonResponse
    {
        $this->authService->logout($request->user());

        return $this->success(null, 'Logged out successfully.');
    }

    // POST /api/auth/logout-all
    public function logoutAll(Request $request): JsonResponse
    {
        $this->authService->logoutAll($request->user());

        return $this->success(null, 'Logged out from all devices.');
    }

    // POST /api/auth/mfa/setup
    public function setupMfa(Request $request): JsonResponse
    {
        $result = $this->authService->setupMfa($request->user());

        return $this->success($result->toArray(), 'Scan the QR code with your authenticator app.');
    }

    // POST /api/auth/mfa/confirm
    public function confirmMfa(MfaVerifyRequest $request): JsonResponse
    {
        try {
            $this->authService->confirmMfa(
                $request->user(),
                $request->validated('otp')
            );

            return $this->success(null, 'MFA enabled successfully.');

        } catch (InvalidMfaCodeException $e) {
            return $this->error($e->getMessage(), 422);
        }
    }

    // POST /api/auth/mfa/disable
    public function disableMfa(DisableMfaRequest $request): JsonResponse
    {
        try {
            $this->authService->disableMfa(
                $request->user(),
                $request->validated('password')
            );

            return $this->success(null, 'MFA disabled.');

        } catch (InvalidCredentialsException $e) {
            return $this->forbidden($e->getMessage());
        }
    }
}
