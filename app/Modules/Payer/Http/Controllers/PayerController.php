<?php

namespace App\Modules\Payer\Http\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Payer\DTOs\PayerDTO;
use App\Modules\Payer\Exceptions\PayerNotFoundException;
use App\Modules\Payer\Http\Requests\PayerRequest;
use App\Modules\Payer\Services\PayerService;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Str;

class PayerController extends Controller
{
    use ApiResponse;
    public function __construct(
        private readonly PayerService $payerService,
    ) {
    }

    public function index(){
        $payer = $this->payerService->getPayers();
        return $this->success($payer, "Payer found");
    }

    public function store(PayerRequest $request): JsonResponse
    {
        $data = $request->validated();
        $dto = PayerDTO::fromRequest($data);
        $result = $this->payerService->store($dto);
        return $this->success($result->toArray(), 'Payer Created Successful..');
    }

    public function update(PayerRequest $request, int $id): JsonResponse
    {
        try {
            $dto = PayerDTO::fromRequest($request->validated());
            $result = $this->payerService->update($id, $dto);
            return $this->success($result->toArray(), 'Payer Updated Successful..');
        } catch (PayerNotFoundException $e) {
            return $this->error($e->getMessage(), 404);
        }
    }

    public function destroy(int $id): JsonResponse
    {
        try {
            $this->payerService->delete($id);
            return $this->success('Payer Deleted Successful..');

        } catch (PayerNotFoundException $e) {
            return $this->error($e->getMessage(), 404);

        }
    }
}