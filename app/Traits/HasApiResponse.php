<?php

namespace App\Traits;

use App\Supports\ApiResponse;
use Illuminate\Http\JsonResponse;

trait HasApiResponse
{
    protected function success(mixed $data = null, string $message = 'Success', int $status = 200, array $meta = []): JsonResponse
    {
        return ApiResponse::success($data, $message, $status, $meta);
    }

    protected function created(mixed $data = null, string $message = 'Resource created successfully'): JsonResponse
    {
        return ApiResponse::created($data, $message);
    }

    protected function noContent(string $message = 'No content'): JsonResponse
    {
        return ApiResponse::noContent($message);
    }

    protected function paginated(mixed $data, array $pagination, string $message = 'Data retrieved successfully'): JsonResponse
    {
        return ApiResponse::paginated($data, $pagination, $message);
    }

    protected function error(string $message = 'An error occurred', int $status = 400, mixed $errors = null, ?string $errorCode = null): JsonResponse
    {
        return ApiResponse::error($message, $status, $errors, $errorCode);
    }

    protected function validationError(mixed $errors, string $message = 'Validation failed'): JsonResponse
    {
        return ApiResponse::validationError($errors, $message);
    }

    protected function unauthorized(string $message = 'Unauthorized'): JsonResponse
    {
        return ApiResponse::unauthorized($message);
    }

    protected function forbidden(string $message = 'Forbidden'): JsonResponse
    {
        return ApiResponse::forbidden($message);
    }

    protected function notFound(string $message = 'Resource not found'): JsonResponse
    {
        return ApiResponse::notFound($message);
    }

    protected function conflict(string $message = 'Resource already exists'): JsonResponse
    {
        return ApiResponse::conflict($message);
    }

    protected function tooManyRequests(string $message = 'Too many requests'): JsonResponse
    {
        return ApiResponse::tooManyRequests($message);
    }
}
