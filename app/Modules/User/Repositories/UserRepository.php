<?php

namespace App\Modules\User\Repositories;

use App\Modules\User\Contracts\UserRepositoryContract;
use App\Modules\User\DTOs\UserDTO;
use App\Modules\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

class UserRepository implements UserRepositoryContract
{
    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return User::latest()
            ->when(! empty($filters['search']), fn($q) => $q->where('name', 'LIKE', "%{$filters['search']}%"))
            ->paginate($perPage);
    }

    public function findOrFail(int|string $id): User
    {
        return User::findOrFail($id);
    }

    public function create(UserDTO $dto): User
    {
        return User::create($dto->toArray());
    }

    public function update(User $model, UserDTO $dto): User
    {
        $model->update($dto->toArray());

        return $model->fresh();
    }

    public function delete(User $model): void
    {
        $model->delete();
    }
}
