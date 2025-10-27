<?php

namespace App\Repositories\Posts;

use App\Models\Post;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Pagination\LengthAwarePaginator;

class PostRepository implements PostRepositoryInterface
{
    public function __construct(protected Post $model)
    {

    }

    public function all(array $columns = ['*']): Collection
    {
        return $this->model->get($columns);
    }

    public function paginate(int $perPage = 15): LengthAwarePaginator
    {
        return $this->model->paginate($perPage);
    }

    public function find(int $id): Post
    {
        return $this->model->findOrFail($id);
    }

    public function create(array $data): Post
    {
        return $this->model->create($data);
    }

    public function update(Post $post, array $data): Post
    {
        $post->update($data);
        return $post;
    }

    public function delete(Post $post): bool
    {
        return (bool) $post->delete();
    }
}
