<?php

namespace App\Modules\User\Contracts;

use App\Modules\User\DTOs\UserDTO;
use App\Modules\User\Models\User;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryContract
{
    public function paginate(int $perPage, array $filters): LengthAwarePaginator;

    public function findOrFail(int|string $id): User;

    public function create(UserDTO $dto): User;

    public function update(User $model, UserDTO $dto): User;

    public function delete(User $model): void;
}