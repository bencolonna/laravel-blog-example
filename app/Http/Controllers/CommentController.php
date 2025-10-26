<?php

namespace App\Http\Controllers;

use App\Http\Requests\Comments\CreateCommentRequest;
use App\Http\Requests\Comments\UpdateCommentRequest;
use App\Http\Resources\Comments\CommentResource;
use App\Http\Resources\Comments\CommentResourceCollection;
use App\Repositories\Comments\CommentRepositoryInterface;
use Exception;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Http\JsonResponse;

class CommentController extends Controller
{
    public function __construct(protected CommentRepositoryInterface $commentRepository)
    {

    }

    public function index(int $postId): JsonResponse|CommentResourceCollection
    {
        try {
            $comments = $this->commentRepository
                ->paginateByPost($postId, 10);

            return new CommentResourceCollection($comments);
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while fetching comments.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function create(CreateCommentRequest $request, int $postId): JsonResponse|CommentResource
    {
        try {
            $data = $request->validated();
            $data['post_id'] = $postId;

            $comment = $this->commentRepository
                ->create($data);

            return new CommentResource(resource: $comment);
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while creating a comment.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function update(UpdateCommentRequest $request, int $postId, int $commentId): JsonResponse|CommentResource
    {
        try {
            $comment = $this->commentRepository
                ->update($commentId, $request->validated());

            return new CommentResource($comment);
        } catch (ModelNotFoundException $ex) {
            return new JsonResponse(
                ['error' => 'Could not find comment.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while updating a comment.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }

    public function delete(int $postId, int $commentId): JsonResponse
    {
        try {
            $result = $this->commentRepository
                ->delete($commentId);

            return new JsonResponse(
                ['deleted' => $result],
                JsonResponse::HTTP_OK
            );
        } catch (ModelNotFoundException $ex) {
            return new JsonResponse(
                ['error' => 'Could not find comment.'],
                JsonResponse::HTTP_NOT_FOUND
            );
        } catch (Exception $ex) {
            report($ex);
            return new JsonResponse(
                ['error' => 'An error occurred while deleting a comment.'],
                JsonResponse::HTTP_UNPROCESSABLE_ENTITY
            );
        }
    }
}
