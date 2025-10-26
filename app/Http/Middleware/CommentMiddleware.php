<?php

namespace App\Http\Middleware;

use App\Repositories\Comments\CommentRepositoryInterface;
use Closure;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CommentMiddleware
{
    public function __construct(protected CommentRepositoryInterface $commentRepository)
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
            $commentId = $request->route('commentId');
            $this->commentRepository->find($commentId);
        } catch (ModelNotFoundException $ex) {
            return new JsonResponse(
                ['error' => 'Could not find comment.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        }

        return $next($request);
    }
}
