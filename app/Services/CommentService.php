<?php

namespace App\Services;

use App\Exceptions\AuthException;
use App\Models\Comment;
use App\Repositories\Comments\CommentRepositoryInterface;

class CommentService
{
    public function __construct(
        protected CommentRepositoryInterface $commentRepository,
        protected AuthService $authService
    ) {

    }

    public function createComment(int $postId, array $data): Comment
    {
        $data['post_id'] = $postId;
        $data['user_id'] = $this->authService->getLoggedInUser()->getId();

        return $this->commentRepository->create($data);
    }

    public function updateComment(int $commentId, array $data): Comment
    {
        $comment = $this->commentRepository->find($commentId);

        if ($this->authService->getLoggedInUser()->getId() !== $comment->getUser()->getId()) {
            throw new AuthException('Unauthorised to edit this post');
        }

        return $this->commentRepository->update($comment, $data);
    }

    public function deleteComment(int $commentId): bool
    {
        $comment = $this->commentRepository->find($commentId);

        if ($this->authService->getLoggedInUser()->getId() !== $comment->getUser()->getId()) {
            throw new AuthException('Unauthorised to edit this post');
        }

        return $this->commentRepository->delete($comment);
    }
}
