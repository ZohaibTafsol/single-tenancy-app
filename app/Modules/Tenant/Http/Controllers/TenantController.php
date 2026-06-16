<?php

namespace App\Modules\Tenant\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Tenant\DTOs\{TenantDTO, DomainDTO};
use App\Modules\Tenant\Http\Requests\CreateTenantRequest;
use App\Modules\Tenant\Http\Requests\UpdateTenantRequest;
use App\Modules\Tenant\Services\TenantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TenantController extends Controller
{
    public function __construct(
        private readonly TenantService $tenantService,
    ) {}

    /** GET /api/tenants */
    public function index(Request $request): JsonResponse
    {
        $data = $this->tenantService->paginate(
            (int) $request->query('per_page', 15)
        );

        return $this->success($data, "Tenant Data");
    }

    /** POST /api/tenants */
    public function store(CreateTenantRequest $request): JsonResponse
    {
        $data    = $request->validated();
        $result = $this->tenantService->create(
            TenantDTO::fromRequest($data),
            DomainDTO::fromRequest($data),
        );
        return response()->json($result, 201);
    }

    /** GET /api/tenants/{id} */
    public function show(int|string $id): JsonResponse
    {
        $model = $this->tenantService->findOrFail($id);

        return response()->json($model);
    }

    /** PUT|PATCH /api/tenants/{id} */
    public function update(UpdateTenantRequest $request, int|string $id): JsonResponse
    {
        $model  = $this->tenantService->findOrFail($id);
        $dto    = TenantDTO::fromRequest($request->validated());
        $result = $this->tenantService->update($model, $dto);

        return response()->json($result);
    }

    /** DELETE /api/tenants/{id} */
    public function destroy(int|string $id): JsonResponse
    {
        $model = $this->tenantService->findOrFail($id);
        $this->tenantService->delete($model);

        return response()->json(null, 204);
    }
}
