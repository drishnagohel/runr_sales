<?php

namespace App\Models;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;

class Login extends Authenticatable
{
    protected $table = 'tbl_user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id','name','last_name','email','mobile_number','password','guid','created_at','updated_at','created_by','updated_by','status'
    ];
}
