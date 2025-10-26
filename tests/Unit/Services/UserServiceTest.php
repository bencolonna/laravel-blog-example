<?php

namespace Tests\Unit\Services;

use App\Models\User;
use App\Repositories\Users\UserRepositoryInterface;
use App\Services\UserService;
use Mockery;
use Tests\TestCase;

class UserServiceTest extends TestCase
{
    protected function tearDown(): void
    {
        Mockery::close();
        parent::tearDown();
    }

    public function test_register_user_hashes_password_and_returns_user()
    {
        $data = ['name' => 'Name', 'email' => 'test@example.com', 'password' => 'Password123'];
        $userMock = Mockery::mock(User::class);

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('create')
            ->once()
            ->with(Mockery::on(function ($arg) use ($data) {
                if (!isset($arg['password'])) {
                    return false;
                }
                if ($arg['email'] !== $data['email']) {
                    return false;
                }
                return password_verify($data['password'], $arg['password']);
            }))
            ->andReturn($userMock);

        $service = new UserService($repo);
        $result = $service->registerUser($data);

        $this->assertSame($userMock, $result);
    }

    public function test_update_user_hashes_password_when_present_and_returns_user()
    {
        $userId = 1;
        $data = ['name' => 'Name 1', 'password' => 'newpass'];
        $expected = Mockery::mock(User::class);

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('update')
            ->once()
            ->with($userId, Mockery::on(function ($arg) use ($data) {
                if (!isset($arg['password'])) {
                    return false;
                }
                if ($arg['name'] !== $data['name']) {
                    return false;
                }
                return password_verify($data['password'], $arg['password']);
            }))
            ->andReturn($expected);

        $service = new UserService($repo);
        $result = $service->updateUser($userId, $data);

        $this->assertSame($expected, $result);
    }

    public function test_update_user_does_not_change_password_when_not_provided()
    {
        $userId = 1;
        $data = ['name' => 'Name 1'];
        $expected = Mockery::mock(User::class);

        $repo = Mockery::mock(UserRepositoryInterface::class);
        $repo->shouldReceive('update')
            ->once()
            ->with($userId, Mockery::on(function ($arg) use ($data) {
                if (array_key_exists('password', $arg)) {
                    return false;
                }
                return $arg['name'] === $data['name'];
            }))
            ->andReturn($expected);

        $service = new UserService($repo);
        $result = $service->updateUser($userId, $data);

        $this->assertSame($expected, $result);
    }
}
