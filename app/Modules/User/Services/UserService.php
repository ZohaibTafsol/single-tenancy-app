<?php

namespace App\Modules\User\Services;

use App\Modules\User\Contracts\UserRepositoryContract;
use App\Modules\User\DTOs\UserDTO;
use App\Modules\User\Models\User;
use App\Modules\User\DTOs\TenantUserDTO;
use Illuminate\Pagination\LengthAwarePaginator;

class UserService
{
    public function __construct(
        private readonly UserRepositoryContract $repository,
    ) {}

    public function paginate(int $perPage = 15, array $filters = []): LengthAwarePaginator
    {
        return $this->repository->paginate($perPage, $filters);
    }

    public function findOrFail(int|string $id): User
    {
        return $this->repository->findOrFail($id);
    }

    public function create(UserDTO $dto): User
    {
        return $this->repository->create($dto);
    }
    public function update(User $model, UserDTO $dto): User
    {
        return $this->repository->update($model, $dto);
    }

    public function delete(User $model): void
    {
        $this->repository->delete($model);
    }
}
