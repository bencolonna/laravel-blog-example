<?php

namespace Tests\Feature\Repositories;

use App\Models\Post;
use App\Repositories\Posts\PostRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class PostRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected PostRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new PostRepository(new Post());
    }

    public function test_all_returns_all_posts(): void
    {
        Post::factory()->count(5)->create();

        $results = $this->repository->all();

        $this->assertCount(5, $results);
        foreach ($results as $post) {
            $this->assertInstanceOf(Post::class, $post);
        }
    }

    public function test_paginate_returns_length_aware_paginator(): void
    {
        Post::factory()->count(25)->create();

        $paginator = $this->repository->paginate(10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(25, $paginator->total());
        $this->assertCount(10, $paginator->items());
    }

    public function test_create_persists_and_returns_post(): void
    {
        $data = Post::factory()->make()->toArray();

        $post = $this->repository->create($data);

        $this->assertInstanceOf(Post::class, $post);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => $data['title'],
            'body' => $data['body']
        ]);
    }

    public function test_find_returns_post(): void
    {
        $post = Post::factory()->create();

        $found = $this->repository->find($post->id);

        $this->assertInstanceOf(Post::class, $found);
        $this->assertEquals($post->id, $found->id);
    }

    public function test_update_modifies_post(): void
    {
        $post = Post::factory()->create();

        $updated = $this->repository->update($post, [
            'title' => 'Updated Title',
            'body' => 'Updated Body'
        ]);

        $this->assertInstanceOf(Post::class, $updated);
        $this->assertEquals('Updated Title', $updated->title);
        $this->assertDatabaseHas('posts', [
            'id' => $post->id,
            'title' => 'Updated Title',
            'body' => 'Updated Body'
        ]);
    }

    public function test_delete_removes_post(): void
    {
        $post = Post::factory()->create();

        $result = $this->repository->delete($post);

        $this->assertTrue($result);
        $this->assertSoftDeleted('posts', ['id' => $post->id]);
    }
}
