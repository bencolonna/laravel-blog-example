<?php

namespace App\Http\Middleware;

use App\Repositories\Posts\PostRepositoryInterface;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class PostMiddleware
{
    public function __construct(protected PostRepositoryInterface $postRepository)
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
            $postId = $request->route('postId');
            $this->postRepository->find($postId);
        } catch (ModelNotFoundException $ex) {
            return new JsonResponse(
                ['error' => 'Could not find post.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        return $next($request);
    }
}
