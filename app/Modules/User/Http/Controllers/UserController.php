<?php

namespace App\Modules\User\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\User\DTOs\UserDTO;
use App\Modules\User\Http\Requests\CreateUserRequest;
use App\Modules\User\Http\Requests\UpdateUserRequest;
use App\Modules\User\Models\User;
use App\Modules\User\Services\UserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
private readonly UserService $service,
    ) {}

    /** GET /api/users */
    public function index(Request $request): JsonResponse
    {
$data = $this->service->paginate(
    perPage: (int) $request->query('per_page', 15),
    filters: $request->only(['search']),
);

return response()->json($data);
    }

    /** POST /api/users */
    public function store(CreateUserRequest $request): JsonResponse
    {
$dto    = UserDTO::fromRequest($request);
$result = $this->service->create($dto);

return response()->json($result, 201);
    }

    /** GET /api/users/{id} */
    public function show(int|string $id): JsonResponse
    {
$model = $this->service->findOrFail($id);

return response()->json($model);
    }

    /** PUT|PATCH /api/users/{id} */
    public function update(UpdateUserRequest $request, int|string $id): JsonResponse
    {
$model  = $this->service->findOrFail($id);
$dto    = UserDTO::fromRequest($request);
$result = $this->service->update($model, $dto);

return response()->json($result);
    }

    /** DELETE /api/users/{id} */
    public function destroy(int|string $id): JsonResponse
    {
$model = $this->service->findOrFail($id);
$this->service->delete($model);

return response()->json(null, 204);
    }
}