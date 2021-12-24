<?php

namespace App\Models\Post;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class PostContent extends Model
{
    use HasFactory;

    protected $table = "post_content";

    public function type() {
        return $this->hasOne(PostContentType::class, 'id','content_type_id');
    }
}
