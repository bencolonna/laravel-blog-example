<?php

namespace App\Services;

use App\Exceptions\AuthException;
use App\Models\User;
use App\Repositories\Users\UserRepositoryInterface;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthService
{
    public function __construct(protected UserRepositoryInterface $userRepository)
    {

    }

    public function login(string $email, string $password): string
    {
        try {
            return JWTAuth::attempt(['email' => $email, 'password' => $password]);
        } catch (JWTException $ex) {
            report($ex);
            throw new AuthException('Credentials are invalid');
        }
    }

    public function logInUser(): User
    {
        try {
            return JWTAuth::parseToken()->authenticate();
        } catch (JWTException $ex) {
            report($ex);
            throw new AuthException('Failed to get user from token');
        }
    }

    public function getLoggedInUser(): User
    {
        try {
            return Auth::user();
        } catch (JWTException $ex) {
            report($ex);
            throw new AuthException('Failed to get user from session');
        }
    }

    public function checkLoggedIn(): bool
    {
        try {
            return Auth::check();
        } catch (JWTException $ex) {
            report($ex);
            throw new AuthException('Failed to get user from session');
        }
    }

    public function logout(): void
    {
        try {
            JWTAuth::invalidate(JWTAuth::getToken());
        } catch (JWTException $ex) {
            report($ex);
            throw new AuthException('Failed to logout, please try again');
        }
    }
}
