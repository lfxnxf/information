<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class LoginLogModel extends Model
{
    protected $table = 'login_log';

    public $timestamps = false;

    public $fillable = ['id', 'admin_user_id', 'create_at', 'ip'];
}
