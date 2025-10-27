<?php

namespace Tests\Feature\Repositories;

use App\Models\Comment;
use App\Models\Post;
use App\Repositories\Comments\CommentRepository;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Tests\TestCase;

class CommentRepositoryTest extends TestCase
{
    use RefreshDatabase;

    protected CommentRepository $repository;

    protected function setUp(): void
    {
        parent::setUp();
        $this->repository = new CommentRepository(new Comment());
    }

    public function test_all_by_post_returns_comments_for_post(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->count(3)->create(['post_id' => $post->id]);

        $other = Post::factory()->create();
        Comment::factory()->count(2)->create(['post_id' => $other->id]);

        $results = $this->repository->allByPost($post->id);

        $this->assertCount(3, $results);
        foreach ($results as $comment) {
            $this->assertInstanceOf(Comment::class, $comment);
            $this->assertEquals($post->id, $comment->post_id);
        }
    }

    public function test_paginate_by_post_returns_length_aware_paginator(): void
    {
        $post = Post::factory()->create();
        Comment::factory()->count(25)->create(['post_id' => $post->id]);

        $paginator = $this->repository->paginateByPost($post->id, 10);

        $this->assertInstanceOf(LengthAwarePaginator::class, $paginator);
        $this->assertEquals(25, $paginator->total());
        $this->assertCount(10, $paginator->items());
    }

    public function test_create_persists_and_returns_comment(): void
    {
        $post = Post::factory()->create();
        $data = Comment::factory()->make([
            'post_id' => $post->id
        ])->toArray();

        $comment = $this->repository->create($data);

        $this->assertInstanceOf(Comment::class, $comment);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'name' => $data['name'],
            'comment' => $data['comment'],
            'post_id' => $post->id
        ]);
    }

    public function test_find_returns_comment(): void
    {
        $comment = Comment::factory()->create();

        $found = $this->repository->find($comment->id);

        $this->assertInstanceOf(Comment::class, $found);
        $this->assertEquals($comment->id, $found->id);
    }

    public function test_update_modifies_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $updated = $this->repository->update($comment, ['comment' => 'New comment']);

        $this->assertInstanceOf(Comment::class, $updated);
        $this->assertEquals('New comment', $updated->comment);
        $this->assertDatabaseHas('comments', [
            'id' => $comment->id,
            'comment' => 'New comment',
            'post_id' => $post->id
        ]);
    }

    public function test_delete_removes_comment(): void
    {
        $post = Post::factory()->create();
        $comment = Comment::factory()->create(['post_id' => $post->id]);

        $result = $this->repository->delete($comment);

        $this->assertTrue($result);
        $this->assertSoftDeleted('comments', ['id' => $comment->id]);
    }
}
