<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\CreateUserRequest;
use App\Http\Resources\Users\UserResource;
use App\Repositories\Users\UserRepositoryInterface;
use App\Services\UserService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class UserController extends Controller
{
    public function __construct(
        protected UserRepositoryInterface $userRepository,
        protected UserService $userService
    ) {

    }

    public function create(CreateUserRequest $request): JsonResponse|UserResource
    {
        try {
            $user = $this->userService
                ->registerUser($request->validated());

            return new UserResource($user);
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while creating a user.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function show(int $userId): JsonResponse|UserResource
    {
        try {
            $user = $this->userRepository
                ->find($userId);

            return new UserResource($user);
        } catch (ModelNotFoundException $ex) {
            return new JsonResponse(
                ['error' => 'Could not find user.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while fetching the user.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
