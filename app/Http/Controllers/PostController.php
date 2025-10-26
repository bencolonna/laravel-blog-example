<?php

namespace App\Http\Controllers;

use App\Exceptions\AuthException;
use App\Http\Requests\Posts\CreatePostRequest;
use App\Http\Requests\Posts\UpdatePostRequest;
use App\Http\Resources\Posts\PostResource;
use App\Http\Resources\Posts\PostResourceCollection;
use App\Repositories\Posts\PostRepositoryInterface;
use App\Services\PostService;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class PostController extends Controller
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected PostService $postService
    ) {

    }

    public function index(): JsonResponse|PostResourceCollection
    {
        try {
            $posts = $this->postRepository
                ->paginate(10);

            return new PostResourceCollection($posts);
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while fetching posts.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function create(CreatePostRequest $request): JsonResponse|PostResource
    {
        try {
            $post = $this->postService
                ->createPost($request->validated());

            return new PostResource($post);
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while creating a post.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function show(int $postId): JsonResponse|PostResource
    {
        try {
            $post = $this->postRepository
                ->find($postId);

            return new PostResource($post);
        } catch (ModelNotFoundException $ex) {
            return new JsonResponse(
                ['error' => 'Could not find post.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while fetching the post.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function update(UpdatePostRequest $request, int $postId): JsonResponse|PostResource
    {
        try {
            $post = $this->postService
                ->updatePost($postId, $request->validated());

            return new PostResource($post);
        } catch (ModelNotFoundException $ex) {
            return new JsonResponse(
                ['error' => 'Could not find post.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (AuthException $ex) {
            return new JsonResponse(
                ['error' => 'Unauthorized'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while updating a post.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function delete(int $postId): JsonResponse
    {
        try {
            $result = $this->postService
                ->deletePost($postId);

            return new JsonResponse(
                ['deleted' => $result],
                JsonResponse::HTTP_OK
            );
        } catch (ModelNotFoundException $ex) {
            return new JsonResponse(
                ['error' => 'Could not find post.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (AuthException $ex) {
            return new JsonResponse(
                ['error' => 'Unauthorized'],
                JsonResponse::HTTP_UNAUTHORIZED
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while deleting a post.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
