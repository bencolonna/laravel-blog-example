<?php

namespace App\Services;

use App\Exceptions\AuthException;
use App\Models\Post;
use App\Repositories\Posts\PostRepositoryInterface;

class PostService
{
    public function __construct(
        protected PostRepositoryInterface $postRepository,
        protected AuthService $authService
    ) {

    }

    public function createPost(array $data): Post
    {
        $data['user_id'] = $this->authService->getLoggedInUser()->getId();

        return $this->postRepository->create($data);
    }

    public function updatePost(int $postId, array $data): Post
    {
        $post = $this->postRepository->find($postId);

        if ($this->authService->getLoggedInUser()->getId() !== $post->getUser()->getId()) {
            throw new AuthException('Unauthorised to edit this post');
        }

        return $this->postRepository->update($post, $data);
    }

    public function deletePost(int $postId): bool
    {
        $post = $this->postRepository->find($postId);

        if ($this->authService->getLoggedInUser()->getId() !== $post->getUser()->getId()) {
            throw new AuthException('Unauthorised to edit this post');
        }

        return $this->postRepository->delete($post);
    }
}
