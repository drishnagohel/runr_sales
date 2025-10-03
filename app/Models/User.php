<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Illuminate\Support\Facades\DB;

class User extends Authenticatable
{
    use HasApiTokens, HasFactory, Notifiable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $table = 'tbl_user';
    protected $primaryKey = 'user_id';
    protected $fillable = [
        'name',
        'last_name',
        'email',
        'password',
        'mobile_number',
        'created_by',
        'updated_by'
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var array<int, string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * The attributes that should be cast.
     *
     * @var array<string, string>
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    public static function getuser($data)
    {

        $query = DB::table('tbl_user as us')
        ->select('us.*');

        if (array_key_exists('user_id', $data) && isset($data['user_id'])) {
            $query = $query->where('us.user_id', '=', $data['user_id']);
        }
        
        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $start = date("Y-m-d", strtotime($data['from_date']));
            $end = date("Y-m-d", strtotime($data['to_date'] . "+1 day"));
            $query = $query->whereBetween('us.created_at', array($start, $end));
        }
        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $query = $query->whereBetween(DB::raw('DATE(us.created_at)'), array($data['from_date'], $data['to_date']));
        }
        if (array_key_exists('status', $data) && isset($data['status'])) {
            $query = $query->where('us.status', '=', $data['status']);
        } else {
            $query = $query->where('us.status', '=', '1');
        }
        if (array_key_exists('sortby', $data) && isset($data['sortby']) && array_key_exists('sorttype', $data) && isset($data['sorttype'])) {
            $query = $query->orderBy('us.' . $data['sortby'], $data['sorttype']);
        }

        $total_count = $query->count();

        if (array_key_exists('offset', $data) && isset($data['offset']) && array_key_exists('limit', $data) && isset($data['limit'])) {
            $query = $query->offset($data['offset'])->limit($data['limit']);
        }

        $result = $query->get();
        $data[0] = $total_count;
        $data[1] = $result;
        return $data;
    }
}
