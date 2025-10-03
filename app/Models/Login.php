<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Login extends Model
{
    protected $table = 'tbl_user';
    protected $primaryKey = 'user_id';

    protected $fillable = [
        'user_id','name','last_name','email','mobile_number','password','guid','created_at','updated_at','created_by','updated_by','status'
    ];
}
