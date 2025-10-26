<?php

namespace App\Repositories\Users;

use App\Models\User;

class UserRepository implements UserRepositoryInterface
{
    public function __construct(protected User $model)
    {

    }

    public function find(int $id): User
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): User
    {
        return $this->model->create($data);
    }

    public function update(int $id, array $data): User
    {
        $post = $this->model->findOrFail($id);
        $post->update($data);
        return $post;
    }
}
