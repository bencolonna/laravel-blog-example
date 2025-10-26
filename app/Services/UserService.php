<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Arr;

class UserService
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {

    }

    public function registerUser(array $data): User
    {
        $data['password'] = bcrypt($data['password']);

        return $this->userRepository->create($data);
    }

    public function updateUser(int $userId, array $data): User
    {
        if (Arr::exists($data, 'password')) {
            $data['password'] = bcrypt($data['password']);
        }

        return $this->userRepository->update($userId, $data);
    }
}
