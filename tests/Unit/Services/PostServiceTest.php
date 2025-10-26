<?php

namespace Tests\Unit\Services;

use App\Exceptions\AuthException;
use App\Models\Post;
use App\Models\User;
use App\Repositories\Posts\PostRepositoryInterface;
use App\Services\AuthService;
use App\Services\PostService;
use Mockery;
use Tests\TestCase;

class PostServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    protected function getUserMockWithId(int $id, int $times = 1)
    {
        $userMock = Mockery::mock(User::class);
        $userMock->shouldReceive('getId')
            ->times($times)
            ->with()
            ->andReturn($id);

        return $userMock;
    }

    public function test_create_post_adds_user_id_and_returns_post(): void
    {
        $data = ['title' => 'Test title', 'body' => 'Test body'];
        $userId = 1;
        $userMock = $this->getUserMockWithId($userId);

        $postMock = Mockery::mock(Post::class);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($this->callback(function ($data) use ($userId) {
                return isset($data['user_id']) && $data['user_id'] === $userId;
            }))
            ->andReturn($postMock);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $service = new PostService($repository, $auth);
        $result = $service->createPost($data);

        $this->assertSame($postMock, $result);
    }

    public function test_update_post_when_authorised_updates_and_returns_post(): void
    {
        $data = ['title' => 'Test title 1', 'body' => 'Test body 1'];
        $userId = 1;
        $userMock = $this->getUserMockWithId($userId, 2);

        $postMock = Mockery::mock(Post::class);
        $postMock->shouldReceive('getUser')
            ->once()
            ->andReturn($userMock);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($postMock);

        $repository->shouldReceive('update')
            ->once()
            ->with($postMock, $data)
            ->andReturn($postMock);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $service = new PostService($repository, $auth);
        $result = $service->updatePost(1, $data);

        $this->assertSame($postMock, $result);
    }

    public function test_update_post_when_unauthorised_throws_exception(): void
    {
        $data = ['title' => 'Test title 1', 'body' => 'Test body 1'];
        $ownerId = 1;
        $userId = 2;

        $ownerUserMock = $this->getUserMockWithId($ownerId);
        $userMock = $this->getUserMockWithId($userId);

        $postMock = Mockery::mock(Post::class);
        $postMock->shouldReceive('getUser')
            ->once()
            ->andReturn($ownerUserMock);


        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($postMock);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $this->expectException(AuthException::class);

        $service = new PostService($repository, $auth);
        $service->updatePost(1, $data);
    }

    public function test_delete_post_when_authorised_updates_and_returns_post(): void
    {
        $userId = 1;
        $userMock = $this->getUserMockWithId($userId, 2);

        $postMock = Mockery::mock(Post::class);
        $postMock->shouldReceive('getUser')
            ->once()
            ->andReturn($userMock);

        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($postMock);

        $repository->shouldReceive('delete')
            ->once()
            ->with($postMock)
            ->andReturn(true);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $service = new PostService($repository, $auth);
        $result = $service->deletePost(1);

        $this->assertSame(true, $result);
    }

    public function test_delete_post_when_unauthorised_throws_exception(): void
    {
        $ownerId = 1;
        $userId = 2;

        $ownerUserMock = $this->getUserMockWithId($ownerId);
        $userMock = $this->getUserMockWithId($userId);

        $postMock = Mockery::mock(Post::class);
        $postMock->shouldReceive('getUser')
            ->once()
            ->andReturn($ownerUserMock);


        $repository = Mockery::mock(PostRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($postMock);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $this->expectException(AuthException::class);

        $service = new PostService($repository, $auth);
        $result = $service->deletePost(1);
    }
}
