<?php

namespace App\Models\Tipoloji;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class UserTipolojiPoint extends Model
{
    use HasFactory;

    protected $table = "user_tipoloji_point";

    public function resultType() {
        return $this->hasOne(TipolojiResultType::class, 'id', 'reault_type_id');
    }
}
