<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;
use DB;
class Sales extends Model
{
    protected $table = 'tbl_sales_details';
    protected $primaryKey = 'sales_details_id';

    protected $fillable = [
        'sales_details_id',
        'total_time',
        'link',
        'sales_date',
        'sales_person',
        'creator',
        'smm',
        'caption',
        'created_by',
        'updated_by',
        'created_at',
        'updated_at',
        'guid',
        'status'
    ];

    public static function getsales($data)
    {

        $query = DB::table('tbl_sales_details as sd')->select('sd.*', 'u.name as createdby_fname', 'u.last_name as createdby_lname', 'us.name as updatedby_fname', 'us.last_name as updatedby_lname')
            ->leftjoin('tbl_user as u', 'sd.created_by', '=', 'u.user_id')
            ->leftjoin('tbl_user as us', 'sd.updated_by', '=', 'us.user_id');

        if (array_key_exists('sales_details_id', $data) && isset($data['sales_details_id'])) {
            $query = $query->where('sd.sales_details_id', '=', $data['sales_details_id']);
        }

        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $start = date("Y-m-d", strtotime($data['from_date']));
            $end = date("Y-m-d", strtotime($data['to_date'] . "+1 day"));
            $query = $query->whereBetween('sd.created_at', array($start, $end));
        }
        if (array_key_exists('from_date', $data) && isset($data['from_date']) && array_key_exists('to_date', $data) && isset($data['to_date'])) {
            $query = $query->whereBetween(DB::raw('DATE(sd.created_at)'), array($data['from_date'], $data['to_date']));
        }
        if (array_key_exists('status', $data) && isset($data['status'])) {
            $query = $query->where('sd.status', '=', $data['status']);
        } else {
            $query = $query->where('sd.status', '=', '1');
        }
        if (array_key_exists('sortby', $data) && isset($data['sortby']) && array_key_exists('sorttype', $data) && isset($data['sorttype'])) {
            $query = $query->orderBy('sd.' . $data['sortby'], $data['sorttype']);
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
