<?php

namespace App\Repositories\Comments;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

interface CommentRepositoryInterface
{
    public function allByPost(int $postId, array $columns = ['*']): Collection;
    public function paginateByPost(int $postId, int $perPage = 15): LengthAwarePaginator;
    public function create(array $data): Comment;
    public function find(int $id): Comment;
    public function update(int $id, array $data): Comment;
    public function delete(int $id): bool;
}
