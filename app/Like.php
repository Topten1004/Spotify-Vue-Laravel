<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Like extends Model
{
    protected $fillable = ['content_type', 'content_source', 'content_id', 'user_id'];
}
