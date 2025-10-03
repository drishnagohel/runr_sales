<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Client extends Model
{
    protected $table = 'tbl_client';
    protected $primaryKey = 'client_id';

    protected $fillable = [
        'client_id',
        'client_name',
        'client_mobile',
        'client_email',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'guid',
        'status'
    ];

    public static function getclient($data)
    {

        $query = DB::table('tbl_client as c')->select('c.*', 'u.name as createdby_fname', 'u.last_name as createdby_lname', 'us.name as updatedby_fname', 'us.last_name as updatedby_lname')
            ->leftjoin('tbl_user as u', 'c.created_by', '=', 'u.user_id')
            ->leftjoin('tbl_user as us', 'c.updated_by', '=', 'us.user_id');

        if (array_key_exists('client_id', $data) && isset($data['client_id'])) {
            $query = $query->where('c.client_id', '=', $data['client_id']);
        }

        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $start = date("Y-m-d", strtotime($data['from_date']));
            $end = date("Y-m-d", strtotime($data['to_date'] . "+1 day"));
            $query = $query->whereBetween('c.created_at', array($start, $end));
        }
        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $query = $query->whereBetween(DB::raw('DATE(c.created_at)'), array($data['from_date'], $data['to_date']));
        }
        if (array_key_exists('status', $data) && isset($data['status'])) {
            $query = $query->where('c.status', '=', $data['status']);
        } else {
            $query = $query->where('c.status', '=', '1');
        }
        if (array_key_exists('sortby', $data) && isset($data['sortby']) && array_key_exists('sorttype', $data) && isset($data['sorttype'])) {
            $query = $query->orderBy('c.' . $data['sortby'], $data['sorttype']);
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