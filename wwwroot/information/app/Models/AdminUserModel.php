<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class AdminUserModel extends Model
{
    protected $table = 'admin_users';

    public $timestamps = false;

    public $fillable = ['username', 'password', 'create_at', 'source', 'token', 'last_login_at'];
}
