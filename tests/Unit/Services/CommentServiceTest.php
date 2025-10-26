<?php

namespace Tests\Unit\Services;

use App\Exceptions\AuthException;
use App\Models\Comment;
use App\Models\User;
use App\Repositories\Comments\CommentRepositoryInterface;
use App\Services\AuthService;
use App\Services\CommentService;
use Mockery;
use Tests\TestCase;

class CommentServiceTest extends TestCase
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

    public function test_create_comment_adds_user_id_and_returns_comment(): void
    {
        $data = ['comment' => 'Test comment'];
        $postId = 1;
        $userId = 1;
        $userMock = $this->getUserMockWithId($userId);
        $userMock->shouldReceive('getName')
            ->with()
            ->andReturn( 'Test name');

        $commentMock = Mockery::mock(Comment::class);

        $repository = Mockery::mock(CommentRepositoryInterface::class);
        $repository->shouldReceive('create')
            ->once()
            ->with($this->callback(function ($data) use ($userId) {
                return isset($data['user_id']) && $data['user_id'] === $userId;
            }))
            ->andReturn($commentMock);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $service = new CommentService($repository, $auth);
        $result = $service->createComment($postId, $data);

        $this->assertSame($commentMock, $result);
    }

    public function test_update_comment_when_authorised_updates_and_returns_comment(): void
    {
        $data = ['comment' => 'Test comment 1'];
        $userId = 1;
        $userMock = $this->getUserMockWithId($userId, 2);

        $commentMock = Mockery::mock(Comment::class);
        $commentMock->shouldReceive('getUser')
            ->once()
            ->andReturn($userMock);

        $repository = Mockery::mock(CommentRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($commentMock);

        $repository->shouldReceive('update')
            ->once()
            ->with($commentMock, $data)
            ->andReturn($commentMock);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $service = new CommentService($repository, $auth);
        $result = $service->updateComment(1, $data);

        $this->assertSame($commentMock, $result);
    }

    public function test_update_comment_when_unauthorised_throws_exception(): void
    {
        $data = ['comment' => 'Test comment 1'];
        $ownerId = 1;
        $userId = 2;

        $ownerUserMock = $this->getUserMockWithId($ownerId);
        $userMock = $this->getUserMockWithId($userId);

        $commentMock = Mockery::mock(Comment::class);
        $commentMock->shouldReceive('getUser')
            ->once()
            ->andReturn($ownerUserMock);


        $repository = Mockery::mock(CommentRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($commentMock);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $this->expectException(AuthException::class);

        $service = new CommentService($repository, $auth);
        $service->updateComment(1, $data);
    }

    public function test_delete_comment_when_authorised_updates_and_returns_comment(): void
    {
        $userId = 1;
        $userMock = $this->getUserMockWithId($userId, 2);

        $commentMock = Mockery::mock(Comment::class);
        $commentMock->shouldReceive('getUser')
            ->once()
            ->andReturn($userMock);

        $repository = Mockery::mock(CommentRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($commentMock);

        $repository->shouldReceive('delete')
            ->once()
            ->with($commentMock)
            ->andReturn(true);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $service = new CommentService($repository, $auth);
        $result = $service->deleteComment(1);

        $this->assertSame(true, $result);
    }

    public function test_delete_comment_when_unauthorised_throws_exception(): void
    {
        $ownerId = 1;
        $userId = 2;

        $ownerUserMock = $this->getUserMockWithId($ownerId);
        $userMock = $this->getUserMockWithId($userId);

        $commentMock = Mockery::mock(Comment::class);
        $commentMock->shouldReceive('getUser')
            ->once()
            ->andReturn($ownerUserMock);


        $repository = Mockery::mock(CommentRepositoryInterface::class);
        $repository->shouldReceive('find')
            ->once()
            ->with(1)
            ->andReturn($commentMock);

        $auth = Mockery::mock(AuthService::class);
        $auth->shouldReceive('getLoggedInUser')
            ->once()
            ->andReturn($userMock);

        $this->expectException(AuthException::class);

        $service = new CommentService($repository, $auth);
        $result = $service->deleteComment(1);
    }
}
