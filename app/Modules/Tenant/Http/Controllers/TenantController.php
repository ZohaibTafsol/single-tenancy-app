<?php

namespace App\Modules\Tenant\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tenant\DTOs\TenantDTO;
use App\Modules\Tenant\Http\Requests\CreateTenantRequest;
use App\Modules\Tenant\Http\Requests\UpdateTenantRequest;
use App\Modules\Tenant\Services\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(
        private readonly TenantService $service,
    ) {}

    /** GET /api/tenants */
    public function index(Request $request): JsonResponse
    {
        $data = $this->service->paginate(
            (int) $request->query('per_page', 15)
        );

        return $this->success($data, "Tenant Data");
    }

    /** POST /api/tenants */
    public function store(CreateTenantRequest $request): JsonResponse
    {
        $dto    = TenantDTO::fromArray($request->validated());
        return response()->json($dto);
        $result = $this->service->create($dto);

        return response()->json($result, 201);
    }

    /** GET /api/tenants/{id} */
    public function show(int|string $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);

        return response()->json($model);
    }

    /** PUT|PATCH /api/tenants/{id} */
    public function update(UpdateTenantRequest $request, int|string $id): JsonResponse
    {
        $model  = $this->service->findOrFail($id);
        $dto    = TenantDTO::fromArray($request->validated());
        $result = $this->service->update($model, $dto);

        return response()->json($result);
    }

    /** DELETE /api/tenants/{id} */
    public function destroy(int|string $id): JsonResponse
    {
        $model = $this->service->findOrFail($id);
        $this->service->delete($model);

        return response()->json(null, 204);
    }
}
