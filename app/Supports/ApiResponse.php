<?php

namespace App\Supports;

use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;

class ApiResponse
{
    // ─── 2xx Success ────────────────────────────────────────────

    public static function success(
        mixed $data = null,
        string $message = 'Success',
        int $status = 200,
        array $meta = []
    ): JsonResponse {
        $payload = [
            'success' => true,
            'message' => $message,
        ];

        if (!is_null($data)) {
            $payload['data'] = $data;
        }

        if (!empty($meta)) {
            $payload['meta'] = $meta;
        }

        return response()->json($payload, $status);
    }

    public static function created(
        mixed $data = null,
        string $message = 'Resource created successfully'
    ): JsonResponse {
        return self::success($data, $message, 201);
    }

    public static function noContent(string $message = 'No content'): JsonResponse
    {
        return response()->json(['success' => true, 'message' => $message], 204);
    }

    public static function paginated(
        mixed $data,
        array $pagination,
        string $message = 'Data retrieved successfully'
    ): JsonResponse {
        return self::success($data, $message, 200, [
            'pagination' => [
                'total'        => $pagination['total'],
                'per_page'     => $pagination['per_page'],
                'current_page' => $pagination['current_page'],
                'last_page'    => $pagination['last_page'],
                'from'         => $pagination['from'] ?? null,
                'to'           => $pagination['to'] ?? null,
            ],
        ]);
    }

    // ─── 4xx Client Errors ──────────────────────────────────────

    public static function error(
        string $message = 'An error occurred',
        int $status = 400,
        mixed $errors = null,
        ?string $errorCode = null
    ): JsonResponse {
        $payload = [
            'success' => false,
            'message' => $message,
        ];

        if (!is_null($errors)) {
            $payload['errors'] = $errors;
        }

        // Machine-readable error code for frontend handling at scale
        if (!is_null($errorCode)) {
            $payload['error_code'] = $errorCode;
        }

        return response()->json($payload, $status);
    }

    public static function validationError(
        mixed $errors,
        string $message = 'Validation failed'
    ): JsonResponse {
        return self::error($message, 422, $errors, 'VALIDATION_ERROR');
    }

    public static function unauthorized(
        string $message = 'Unauthorized',
        ?string $errorCode = 'UNAUTHORIZED'
    ): JsonResponse {
        return self::error($message, 401, null, $errorCode);
    }

    public static function forbidden(
        string $message = 'You do not have permission to perform this action',
        ?string $errorCode = 'FORBIDDEN'
    ): JsonResponse {
        return self::error($message, 403, null, $errorCode);
    }

    public static function notFound(
        string $message = 'Resource not found',
        ?string $errorCode = 'NOT_FOUND'
    ): JsonResponse {
        return self::error($message, 404, null, $errorCode);
    }

    public static function conflict(
        string $message = 'Resource already exists',
        ?string $errorCode = 'CONFLICT'
    ): JsonResponse {
        return self::error($message, 409, null, $errorCode);
    }

    public static function tooManyRequests(
        string $message = 'Too many requests, please slow down',
        ?string $errorCode = 'RATE_LIMIT_EXCEEDED'
    ): JsonResponse {
        return self::error($message, 429, null, $errorCode);
    }

    // ─── 5xx Server Errors ──────────────────────────────────────

    public static function serverError(
        string $message = 'Internal server error',
        ?\Throwable $exception = null,
        ?string $errorCode = 'SERVER_ERROR'
    ): JsonResponse {
        // Log full exception internally, never expose to client
        if (!is_null($exception)) {
            Log::error('Server Error', [
                'message'   => $exception->getMessage(),
                'file'      => $exception->getFile(),
                'line'      => $exception->getLine(),
                'trace'     => $exception->getTraceAsString(),
            ]);
        }

        return self::error(
            app()->isProduction() ? $message : ($exception?->getMessage() ?? $message),
            500,
            null,
            $errorCode
        );
    }

    public static function serviceUnavailable(
        string $message = 'Service temporarily unavailable'
    ): JsonResponse {
        return self::error($message, 503, null, 'SERVICE_UNAVAILABLE');
    }
}