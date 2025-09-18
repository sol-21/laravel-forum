<?php

namespace TeamTeaTime\Forum\Http\Controllers\Api;


use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;
use TeamTeaTime\Forum\Http\Controllers\Api\BaseController;
use TeamTeaTime\Forum\Http\Requests\VoteRequest;
use TeamTeaTime\Forum\Http\Resources\VoteResource;
use TeamTeaTime\Forum\Models\Post;
use TeamTeaTime\Forum\Models\Vote;

class VoteController extends BaseController {
    public function store(VoteRequest $request) {
        $user = Auth::user();
        $validated = $request->validated();
        $post = Post::findOrFail($validated['post_id']);

        // Create or update vote
        $vote = Vote::updateOrCreate(
            [
                'user_id' => $user->id,
                'post_id' => $post->id,
            ],
            [
                'type' => $validated['type'],
            ]
        );

        // Load user relation for resource
        $vote->load('user');

        return new VoteResource($vote);
    }
}
