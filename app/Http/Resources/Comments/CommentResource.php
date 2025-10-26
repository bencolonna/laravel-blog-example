<?php

namespace App\Http\Resources\Comments;

use App\Models\Comment;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CommentResource extends JsonResource
{
    /**
     * The resource instance.
     *
     * @var Comment
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
            'id' => $this->getId(),
            'name' => $this->getName(),
            'comment' => $this->getComment(),
            'created_at' => $this->getCreatedAt()->format('l j F Y H:i:s')
        ];
    }
}
