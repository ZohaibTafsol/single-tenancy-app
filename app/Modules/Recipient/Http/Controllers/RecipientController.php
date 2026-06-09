<?php

namespace App\Modules\Recipient\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Recipient\DTOs\RecipientDTO;
use App\Modules\Recipient\Exceptions\RecipientNotFoundException;
use App\Modules\Recipient\Http\Requests\RecipientRequest;
use App\Modules\Recipient\Services\RecipientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class RecipientController extends Controller
{

    public function __construct(
        private readonly RecipientService $RecipientService,
    ) {
    }
    public function store(RecipientRequest $request): JsonResponse
    {
        $data = $request->validated();
        $dto = RecipientDTO::fromRequest($data);
        $result = $this->RecipientService->store($dto);
        return $this->success($result->toArray(), 'Recipient Created Successful..');
    }

    public function update(RecipientRequest $request, int $id): JsonResponse
    {
        try {
            $dto = RecipientDTO::fromRequest($request->validated());
            $result = $this->RecipientService->update($id, $dto);
            return $this->success($result->toArray(), 'Recipient Updated Successful..');

        } catch (RecipientNotFoundException $e) {
            return $this->error('Recipient not found', 404, $e->getMessage());

        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->RecipientService->delete($id);
            return $this->success('Recipient Deleted Successful..');

        } catch (RecipientNotFoundException $e) {
            return $this->error('Recipient not found', 404, $e->getMessage());

        }
    }
}