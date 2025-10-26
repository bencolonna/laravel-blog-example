<?php

namespace App\Http\Resources\Posts;

use App\Models\Post;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PostResource extends JsonResource
{
    /**
     * The resource instance.
     *
     * @var Post
     */
    public $resource;

    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->resource->getId(),
            'name' => $this->resource->getTitle(),
            'comment' => $this->resource->getBody(),
            'created_at' => $this->resource->getCreatedAt()->format('l j F Y H:i:s')
        ];
    }
}
