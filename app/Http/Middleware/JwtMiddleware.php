<?php

namespace App\Http\Middleware;

use App\Services\AuthService;
use Closure;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class JwtMiddleware
{
    public function __construct(protected AuthService $authService)
    {

    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        try {
            $this->authService->logInUser();
        } catch (Exception $ex) {
            return new JsonResponse(
                ['error' => 'Unauthorized'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        }

        return $next($request);
    }
}
