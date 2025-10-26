<?php

namespace App\Services;

use App\Models\User;
use App\Repositories\Users\UserRepositoryInterface;

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
}
