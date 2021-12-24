<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Post extends Model
{
    use HasFactory;

    protected $table = "post";

    public function user() {
        return $this->hasOne(User::class, 'user_id', 'user_id')
            ->select('user_id','name','title','image');
    }

    public function postLike() {
        return $this->hasMany(PostLike::class, 'post_id', 'id')
            ->select('post_id','user_id')
            ->orderByDesc('created_at');
    }

    public function bookmark() {
        return $this->hasMany(Bookmark::class, 'post_id', 'id')
            ->select('post_id','user_id');
    }

    public function source() {
        return $this->hasMany(PostSource::class, 'post_id', 'id');
    }

    public function content() {
        return $this->hasMany(PostContent::class, 'post_id', 'id');
    }
}
