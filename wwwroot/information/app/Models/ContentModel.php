<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class ContentModel extends Model
{
    protected $table = 'content';

    public $timestamps = false;

    protected $fillable = ['id', 'title', 'content', 'type', 'img_head', 'create_at', 'modify_at', 'create_user_id', 'source', 'category'];
}