<?php

namespace App\Repositories\Users;

use App\Models\User;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface UserRepositoryInterface
{
    public function find(int $id): User;
    public function create(array $data): User;
    public function update(int $id, array $data): User;
}
