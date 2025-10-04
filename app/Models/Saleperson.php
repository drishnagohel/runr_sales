<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Saleperson extends Model
{
    protected $table = 'tbl_salesperson';
    protected $primaryKey = 'person_id';

    protected $fillable = [
        'person_id',
        'person_name',
        'person_mobile',
        'person_email',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'guid',
        'status'
    ];

    public static function getsalesperson($data)
    {

        $query = DB::table('tbl_salesperson as sp')->select('sp.*', 'u.name as createdby_fname', 'u.last_name as createdby_lname', 'us.name as updatedby_fname', 'us.last_name as updatedby_lname')
            ->leftjoin('tbl_user as u', 'sp.created_by', '=', 'u.user_id')
            ->leftjoin('tbl_user as us', 'sp.updated_by', '=', 'us.user_id');

        if (array_key_exists('person_id', $data) && isset($data['person_id'])) {
            $query = $query->where('sp.person_id', '=', $data['person_id']);
        }
        if (array_key_exists('search', $data) && isset($data['search'])) {
            $searchTerm = $data['search'];
            $query = $query->where(function ($query) use ($searchTerm) {
                $query->orWhere('sp.person_name', 'like', '%' . $searchTerm . '%');
            });
        }
        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $start = date("Y-m-d", strtotime($data['from_date']));
            $end = date("Y-m-d", strtotime($data['to_date'] . "+1 day"));
            $query = $query->whereBetween('sp.created_at', array($start, $end));
        }
        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $query = $query->whereBetween(DB::raw('DATE(sp.created_at)'), array($data['from_date'], $data['to_date']));
        }
        if (array_key_exists('status', $data) && isset($data['status'])) {
            $query = $query->where('sp.status', '=', $data['status']);
        } else {
            $query = $query->where('sp.status', '=', '1');
        }
        if (array_key_exists('sortby', $data) && isset($data['sortby']) && array_key_exists('sorttype', $data) && isset($data['sorttype'])) {
            $query = $query->orderBy('sp.' . $data['sortby'], $data['sorttype']);
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
