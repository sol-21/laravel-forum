<?php

namespace TeamTeaTime\Forum\Models;


use Illuminate\Database\Eloquent\Model;

class Vote extends BaseModel {
    protected $table = 'forum_votes';


    protected $fillable = ['user_id', 'type', 'post_id'];

    public function post() {
        return $this->belongsTo(Post::class);
    }
    public function user() {
        return $this->belongsTo(config('auth.providers.users.model'));
    }
}
