<?php

namespace App\Modules\Recipient\Actions;

use App\Modules\Recipient\Contracts\RecipientRepositoryContract;
use App\Modules\Recipient\DTOs\RecipientDTO;
use App\Modules\Recipient\Models\Recipient;
use App\Modules\Payer\Services\PayerService;

class CreateRecipientAction
{
    public function __construct(
        private readonly RecipientRepositoryContract $recipientRepository,
        private readonly PayerService $payerService,
    ) {}

    public function execute(RecipientDTO $dto): Recipient
    {
        $data = $dto->toArray();
        $payer = $this->payerService->getPayerByUuid($data["payer_uuid"]);
        $data["payer_id"] = $payer->id; 
        unset($data['payer_uuid']);
        return $this->recipientRepository->store($data);
    }
}
