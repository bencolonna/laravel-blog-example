<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthException;
use App\Http\Requests\Users\UpdateUserRequest;
use App\Http\Resources\Users\UserResource;
use App\Services\AuthService;
use App\Services\UserService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AuthController extends Controller
{
    public function __construct(protected AuthService $authService, protected UserService $userService)
    {

    }

    public function login(Request $request): JsonResponse
    {
        $credentials = $request->only('email', 'password');

        try {
            $token = $this->authService->login($credentials['email'], $credentials['password']);

            return new JsonResponse(
                [
                    'token' => $token,
                    'expires_in' => auth('api')->factory()->getTTL() * 60
                ],
                JsonResponse::HTTP_OK
            );
        } catch (AuthException $ex) {
            return new JsonResponse(
                ['error' => 'Your credentials are invalid.'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while logging in.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function logout(): JsonResponse
    {
        try {
            $this->authService->logout();

            return new JsonResponse(
                ['message' => 'Successfully logged out.'],
                JsonResponse::HTTP_OK
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while logging out.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function show(): JsonResponse|UserResource
    {
        try {
            $user = $this->authService->getLoggedInUser();
            if (!$user) {
                $this->authService->logout();
            }

            return new UserResource($user);
        } catch (AuthException $ex) {
            return new JsonResponse(
                ['error' => 'Unauthorized'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while fetching your user.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function update(UpdateUserRequest $request): JsonResponse|UserResource
    {
        try {
            $user = $this->authService->getLoggedInUser();
            if (!$user) {
                $this->authService->logout();
            }

            $user = $this->userService
                ->updateUser($user->getId(), $request->validated());

            return new UserResource($user);
        } catch (AuthException $ex) {
            return new JsonResponse(
                ['error' => 'Unauthorized'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while updating your user.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
