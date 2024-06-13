<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class UserBlog extends Model
{
    use HasFactory,SoftDeletes;
    protected $table = "user_blogs";
    protected $primaryKey = "id";
    protected $fillable = ['id',    'title',    'content',    'slug',    'user_id',    'category_id',    'image'];

    public function getImageAttribute($val)
    {
        return url('images').'/'.$val;
    }
}
