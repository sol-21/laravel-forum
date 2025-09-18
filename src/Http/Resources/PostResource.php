<?php

namespace TeamTeaTime\Forum\Http\Resources;

use App\Http\Resources\VoteResource;
use Illuminate\Http\Resources\Json\JsonResource;
use TeamTeaTime\Forum\Support\Api\ForumApi;

class PostResource extends JsonResource {
    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function toArray($request) {
        return [
            'id' => $this->id,
            'thread_id' => $this->thread_id,
            'thread_title' => $this->thread->title,
            'author_id' => $this->author_id,
            'author_name' => $this->author_name,
            'content' => $this->content,
            'post_id' => $this->post_id,
            'sequence' => $this->sequence,
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
            'deleted_at' => $this->deleted_at,
            'reply_count' => $this->children()->whereNull('deleted_at')->where('sequence', '!=', 1)->count(),
            'children' => PostResource::collection($this->whenLoaded('children')),
            'votes' => VoteResource::collection($this->whenLoaded('votes')),
            'upvotes' => VoteResource::collection($this->whenLoaded('upvotes')),
            'upvote_count' => $this->upvotes->count(),
            'downvotes' => VoteResource::collection($this->whenLoaded('downvotes')),
            'downvote_count' => $this->downvotes->count(),

            'actions' => [
                'patch:update' => ForumApi::route('post.update', ['post' => $this->id]),
                'delete:delete' => ForumApi::route('post.delete', ['post' => $this->id]),
                'post:restore' => ForumApi::route('post.restore', ['post' => $this->id]),
            ],
        ];
    }

    /**
     * @param  \Illuminate\Http\Request  $request
     * @return array
     */
    public function with($request) {
        $links = [
            'self' => ForumApi::route('post.fetch', ['post' => $this->id]),
            'thread' => ForumApi::route('thread.fetch', ['thread' => $this->thread_id]),
        ];

        if ($this->post_id != null) {
            $links['parent'] = ForumApi::route('post.fetch', ['post' => $this->post_id]);
        }

        return compact('links');
    }
}
