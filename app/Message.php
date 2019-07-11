<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Message extends Model
{
    protected $fillable = [
        'body', 'date', 'user_from', 'user_to', 'type', 'path'
    ];
}
