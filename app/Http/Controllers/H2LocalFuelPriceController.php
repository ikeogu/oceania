<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\CommReceipt;
use App\Models\Currency;
use App\Models\Company;
use App\Models\Companycontact;
use App\Models\Companydirector;
use App\Models\Location;
use App\Models\Terminal;
use App\Models\User;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Auth;
use Log;
use Illuminate\Support\Carbon;
use Yajra\DataTables\DataTables;
use Illuminate\Support\Facades\Validator;

class H2LocalFuelPriceController extends Controller
{
    public function local_hydrogenprice()
    {
        $push = DB::table('h2_localfuelprice')->
			select('push_date')->
			orderBy('push_date', 'DESC')->
			first();

        $date = $push->push_date ?? null;

        return view('h2_local_fuelprice.h2_local_fuelprice', compact('date'));
    }


    function showH2LocalPriceDatatable(Request $request)
    {
        $fuelRecord = DB::table('product')->
        leftjoin('prd_h2fuel', 'product.id', 'prd_h2fuel.product_id')->
        leftjoin('h2_localfuelprice', 'h2_localfuelprice.h2fuel_id', 'prd_h2fuel.id')->
        where('product.ptype', 'h2')->
        orderBy('h2_localfuelprice.start', 'desc')->
        select("product.*", 'h2_localfuelprice.h2fuel_id',
            'h2_localfuelprice.price','h2_localfuelprice.controller_price',
            'prd_h2fuel.id as alt_id', 'h2_localfuelprice.user_id',
            'h2_localfuelprice.updated_at as u_date',
            'h2_localfuelprice.id as localfuelprice_id'
        )->
        get();

        return Datatables::of($fuelRecord)->
        addIndexColumn()->
        addColumn('product_systemid', function ($data) {
            return $data->systemid;
        })->
        addColumn('product_name', function ($data) {
            $img_src = '/images/product/' .
                $data->systemid . '/thumb/' .
                $data->thumbnail_1;

            $img = "<img src='$img_src' data-field='inven_pro_name'
                style='width: 25px; height: 25px;display: inline-block;
                margin-right: 8px;object-fit:contain;'>";

            return $img . $data->name;
        })->
        addColumn('controller_price', function ($data) {
            return  number_format(($data->controller_price ?? 0) / 100, 2);
        })->
        addColumn('price', function ($data) {
            $price = number_format(($data->price ?? 0) / 100, 2);
            $fk_id = $data->h2fuel_id;
            if(empty($fk_id)){
                $fk_id =  $data->alt_id;
            }

            $html = <<<EOD
                <span class="os-linkcolor" onclick=
                    "price_set_modal('$price', '$fk_id')"
                    style="cursor:pointer">
                    $price
                </span>
EOD;

            //      $isToday = \Carbon\Carbon::parse(date('Y-m-d',strtotime($data->start)));
            //  return ($isToday->isToday() || $isToday->isFuture()) ? $html:$price;
            return $html;
        })->
        addColumn('user', function ($data) {

            return DB::table('users')->find($data->user_id)->fullname ?? '';
        })->
        addColumn('user_date', function ($data) {
            return !empty($data->u_date) ?
                date("dMy H:i:s", strtotime($data->u_date)) : '';
        })->
        addColumn('loyalty', function ($data) {
            return '';
        })->
        escapeColumns([])->
        make(true);
    }

    function showH2LocalPriceUpdate(Request $request)
    {
        try {
            $company = DB::table('company')->first();
            $location = DB::table('location')->first();
            $validation = Validator::make($request->all(), [
                "field" => "required",
                "value" => "required",
                "id" => "required"
            ]);

            Log::debug('***** showH2LocalPriceUpdate *****');
            Log::debug('all()=' . json_encode($request->all()));
            Log::debug('validation=' . json_encode($validation->fails()));

            if ($validation->fails()) {
                $message = $validation->errors();

                Log::debug('message=' . json_encode($message));

                return response()->json(compact('message'));
            }

            $is_exist = DB::table('h2_localfuelprice')->
                where([
                    "id" => $request->id,
                ])->first();

            Log::debug('is_exist=' . json_encode($is_exist));

            $array = [];
            $array['updated_at'] = date('Y-m-d H:i:s');
            $array["company_id"] = $company->id;
            $array["location_id"] = $location->id;

            switch ($request->field) {
                case 'start':
                    $array['start'] = date("Y-m-d 00:00:00",
                        strtotime($request->value));
                    break;
                case 'price':
                    $array['user_id'] = \Auth::User()->id;
                    $array['price'] = $request->value;
                    break;
                default:
                    throw new \Exception("Invalid data type");
                    break;
            }

            if (!empty($is_exist)) {
                DB::table('h2_localfuelprice')->
                where([
                    "h2fuel_id" => $request->id,
                ])->update($array);
            } else {
                $array['created_at'] = date('Y-m-d H:i:s');
                $array['start'] = date('Y-m-d H:i:s');
                $array["h2fuel_id"] = $request->id;
                $array['user_id'] = \Auth::User()->id;
                DB::table('h2_localfuelprice')->insert($array);
            }

            return response()->json(["status" => true]);

        } catch (\Exception $e) {
            \Log::info([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);
            abort(404);
        }
    }
}
