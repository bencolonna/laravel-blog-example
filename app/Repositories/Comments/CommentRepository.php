<?php

namespace App\Repositories\Comments;

use App\Models\Comment;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class CommentRepository implements CommentRepositoryInterface
{
    public function __construct(protected Comment $model)
    {

    }

    public function allByPost(int $postId, array $columns = ['*']): Collection
    {
        return $this->model
            ->where('post_id', '=', $postId)
            ->all($columns);
    }

    public function paginateByPost(int $postId, int $perPage = 15): LengthAwarePaginator
    {
        return $this->model
            ->where('post_id', '=', value: $postId)
            ->paginate($perPage);
    }

    public function create(array $data): Comment
    {
        return $this->model->create($data);
    }

    public function find(int $id): Comment
    {
        return $this->model->findOrFail($id);
    }

    public function update(int $id, array $data): Comment
    {
        $post = $this->model->findOrFail($id);
        $post->update($data);
        return $post;
    }

    public function delete(int $id): bool
    {
        $post = $this->model->findOrFail($id);
        return (bool) $post->delete();
    }
}
