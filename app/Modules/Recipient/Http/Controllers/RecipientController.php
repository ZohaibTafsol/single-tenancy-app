<?php

namespace App\Modules\Recipient\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Recipient\DTOs\RecipientDTO;
use App\Modules\Recipient\Exceptions\RecipientNotFoundException;
use App\Modules\Recipient\Http\Requests\CreateRecipientRequest;
use App\Modules\Recipient\Http\Requests\RecipientRequest;
use App\Modules\Recipient\Services\RecipientService;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;
use Illuminate\Http\Request;

class RecipientController extends Controller
{

    public function __construct(
        private readonly RecipientService $recipientService,
    ) {}

    public function index(Request $request): JsonResponse
    {
        $filter_params = $request->only(['name', 'email', 'tax_id', 'client_id']);
        $recipients = $this->recipientService->getRecipients($filter_params);
        return $this->success($recipients, "Recipients found");
    }
    public function store(CreateRecipientRequest $request): JsonResponse
    {
        $data = $request->validated();
        $dto = RecipientDTO::fromRequest($data);
        return response()->json($dto->toArray());
        $result = $this->recipientService->store($dto);
        return $this->success($result->toArray(), 'Recipient Created Successful..');
    }

    public function update(RecipientRequest $request, int $id): JsonResponse
    {
        try {
            $dto = RecipientDTO::fromRequest($request->validated());
            $result = $this->recipientService->update($id, $dto);
            return $this->success($result->toArray(), 'Recipient Updated Successful..');
        } catch (RecipientNotFoundException $e) {
            return $this->error('Recipient not found', 404, $e->getMessage());
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->recipientService->delete($id);
            return $this->success('Recipient Deleted Successful..');
        } catch (RecipientNotFoundException $e) {
            return $this->error('Recipient not found', 404, $e->getMessage());
        }
    }
}
