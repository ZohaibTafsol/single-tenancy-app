<?php
namespace App\Modules\Recipient\Repositories;
use App\Modules\Recipient\Constants\RecipientConstants;
use App\Modules\Recipient\Models\Recipient;
use App\Modules\Recipient\Contracts\RecipientRepositoryContract;
use App\Modules\Recipient\DTOs\RecipientDTO;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class RecipientRepository implements RecipientRepositoryContract
{
    public function getRecipients(array $filterData): LengthAwarePaginator
    {
        $query = Recipient::query();

        if (isset($filterData['name'])) {
            $query->where('name', 'like', '%' . $filterData['name'] . '%');
        }

        if (isset($filterData['email'])) {
            $query->where('email', 'like', '%' . $filterData['email'] . '%');
        }

        if (isset($filterData['tax_id'])) {
            $query->where('tax_id', 'like', '%' . $filterData['tax_id'] . '%');
        }

        if (isset($filterData['client_id'])) {
            $query->where('client_id', $filterData['client_id']);
        }

        return $query->where('user_id', $filterData['user_id'])->paginate(RecipientConstants::PER_PAGE);
    }
    public function findById(int $id): ?Recipient
    {
        return Recipient::find($id);
    }
    public function store(array $data): Recipient
    {
        return Recipient::create($data);
    }
    public function update(Recipient $recipient, RecipientDTO $dto): Recipient
    {
        $recipient->update($this->toArray($dto));
        return $recipient->fresh();
    }

    public function delete(Recipient $recipient): void
    {
        $recipient->delete();
    }
}