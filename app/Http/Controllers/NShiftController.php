<?php

namespace App\Http\Controllers;

use App\Imports\NshiftExcelExtractor;
use Illuminate\Http\Request;
use DB;
use Illuminate\Support\Facades\Log;
use Maatwebsite\Excel\Facades\Excel;
use PDF;

class NShiftController extends Controller
{
    //

    public function get_nshift_details_from_id(Request $request){

        $data = DB::table('nshift')->
        leftjoin('users', 'users.systemid', 'nshift.staff_systemid')->
         select(
            'nshift.*',
            'nshift.staff_systemid',
            'nshift.staff_name as staff_name',
            'users.id as staff_id'
        )->
        where('nshift.id',$request->id)->
        first();

        $out = empty($data->out) ? date('Y-m-d H:i:s'): $data->out;

        ExcelExportController::saveExcelNshift($data->staff_id
            ,$data->in,$out ,'Nshift',$data->id,1);

        $file_path = 'fuel/' . $data->in. '-' . $data->staff_id
            . 'nshift_fuel_receipt_list.xlsx';
        Excel::import(new NshiftExcelExtractor($data), storage_path($file_path));
        $receipt_sld = array();
        $parentId = DB::table('nshift_fuelsales_summary')->
            where('shift_in', $data->in)->
            where('shift_no',$data->id)->
            first();

        if (!empty($parentId)) {
            $receipt_sld =  DB::table('nshift_fuelsales_summary_detail')->
                where('nshift_fuelsales_summary_id', $parentId->id)->
                get();
        }
        $cstore = $data->cstore;
        $shift = $parentId;

        $pdf = PDF::loadView('nshift.nshift_fuelsales_summary_pdf', compact(
            'receipt_sld',
            'shift',
            'cstore'
        ))->setPaper('A4', 'portrait');

        return  $pdf->download($shift->shift_in. '-' . $shift->staff_id_systemid .
            'Nshift_FuelSales_Summary.pdf');

    }
}
