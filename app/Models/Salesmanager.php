<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Salesmanager extends Model
{
    protected $table = 'tbl_salesmanger';
    protected $primaryKey = 'salesmanger_id';

    protected $fillable = [
        'salesmanger_id',
        'salesmanger_name',
        'salesmanger_mobile',
        'salesmanger_email',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'guid',
        'status'
    ];

    public static function getsalesmanager($data)
    {

        $query = DB::table('tbl_salesmanger as sm')->select('sm.*', 'u.name as createdby_fname', 'u.last_name as createdby_lname', 'us.name as updatedby_fname', 'us.last_name as updatedby_lname')
            ->leftjoin('tbl_user as u', 'sm.created_by', '=', 'u.user_id')
            ->leftjoin('tbl_user as us', 'sm.updated_by', '=', 'us.user_id');

        if (array_key_exists('salesmanger_id', $data) && isset($data['salesmanger_id'])) {
            $query = $query->where('sm.salesmanger_id', '=', $data['salesmanger_id']);
        }
        if (array_key_exists('search', $data) && isset($data['search'])) {
            $searchTerm = $data['search'];
            $query = $query->where(function ($query) use ($searchTerm) {
                $query->orWhere('sm.salesmanger_name', 'like', '%' . $searchTerm . '%');
            });
        }
        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $start = date("Y-m-d", strtotime($data['from_date']));
            $end = date("Y-m-d", strtotime($data['to_date'] . "+1 day"));
            $query = $query->whereBetween('sm.created_at', array($start, $end));
        }
        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $query = $query->whereBetween(DB::raw('DATE(sm.created_at)'), array($data['from_date'], $data['to_date']));
        }
        if (array_key_exists('status', $data) && isset($data['status'])) {
            $query = $query->where('sm.status', '=', $data['status']);
        } else {
            $query = $query->where('sm.status', '=', '1');
        }
        if (array_key_exists('sortby', $data) && isset($data['sortby']) && array_key_exists('sorttype', $data) && isset($data['sorttype'])) {
            $query = $query->orderBy('sm.' . $data['sortby'], $data['sorttype']);
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
