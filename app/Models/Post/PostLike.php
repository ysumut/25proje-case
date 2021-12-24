<?php

namespace App\Models\Post;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostLike extends Model
{
    use HasFactory;

    protected $table = "post_like";

    public function user() {
        return $this->hasOne(User::class, 'user_id', 'user_id')
            ->select('id','user_id','name','title','image','created_at');
    }
}
