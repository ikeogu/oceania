<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;

class ProductMappingController extends Controller
{
    //
    public function product_mapping(Request $request)
    {
        $pmps = DB::table('local_pump')->
            where('controller_id', '1')->
            whereNull('deleted_at')->
            get();

        $products = DB::table('prd_ogfuel')->
            join('product', 'product.id', 'prd_ogfuel.product_id')->
            where('prd_ogfuel.status', 'active')->
            whereNull('prd_ogfuel.deleted_at')->
            select(
                'prd_ogfuel.id as og_fuel_id',
                'prd_ogfuel.litre',
                'prd_ogfuel.product_id',
                'prd_ogfuel.price',
                'prd_ogfuel.upper_price',
                'prd_ogfuel.lower_price',
                'prd_ogfuel.wholesale_price',
                'prd_ogfuel.status',
                'prd_ogfuel.loyalty',
                'prd_ogfuel.created_at',
                'prd_ogfuel.updated_at',
                'product.systemid',
                'product.name',
                'product.description',
                'product.photo_1',
                'product.thumbnail_1',
                'product.sku',
                'product.ptype',
                'product.brand_id'
            )->
            get();

        $pumps = collect();

        if(!empty($pmps)){
            foreach($pmps as $pump){
                $pump->nozzles = DB::table('local_pumpnozzle')->
                    join('prd_ogfuel', 'prd_ogfuel.id', 'local_pumpnozzle.ogfuel_id')->
                    join('product', 'product.id', 'prd_ogfuel.product_id')->
                    where('local_pumpnozzle.pump_id', $pump->id)->
                    whereNull('local_pumpnozzle.deleted_at')->
                    select(
                        'local_pumpnozzle.id as lpz_id',
                        'local_pumpnozzle.pump_id as lpz_pump_id',
                        'local_pumpnozzle.nozzle_no as lpz_nozzle_no',
                        'local_pumpnozzle.ogfuel_id as lpz_ogfuel_id',
                        'prd_ogfuel.litre as prd_og_litre',
                        'prd_ogfuel.product_id as prd_og_product_id',
                        'prd_ogfuel.price as prd_og_price',
                        'prd_ogfuel.upper_price as prd_og_upper_price',
                        'prd_ogfuel.lower_price as prd_og_lower_price',
                        'prd_ogfuel.wholesale_price as prd_og_wholesale_price',
                        'prd_ogfuel.status as prd_og_status',
                        'prd_ogfuel.loyalty as prd_og_loyalty',
                        'prd_ogfuel.created_at as prd_og_created_at',
                        'prd_ogfuel.updated_at as prd_og_updated_at',
                        'product.systemid',
                        'product.name',
                        'product.description',
                        'product.photo_1',
                        'product.thumbnail_1',
                        'product.sku',
                        'product.ptype',
                        'product.brand_id'
                    )->
                    get();

                $pumps->push($pump);
            }
        }


        Log::info('product_mapping: $pumps=' . json_encode($pumps));

        return view('product_mapping.product_mapping', compact('pumps', 'products'));
    }

    public function save_mapping_info(Request $request){
        try {
            $mapping_exists = DB::table('local_pumpnozzle')->
                where([
                        "pump_id" => $request->pump_id,
                        "nozzle_no" => $request->nozzle_no,
                        "deleted_at" => NULL
                    ])->
                first();

            if (!empty($mapping_exists)) {
                DB::table('local_pumpnozzle')->
                    where('id', $mapping_exists->id)->
                    update([
                        "ogfuel_id" => $request->ogfuel_id,
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            } else {
                $nozzle_data_id = DB::table('local_pumpnozzle')->insertGetId([
                        "pump_id" => $request->pump_id,
                        "nozzle_no" => $request->nozzle_no,
                        "ogfuel_id" => $request->ogfuel_id,
                        "created_at" => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s'),
                    ]);
            }


            return response()->json("save succeessful", 200);
        } catch (\Exception $e) {
            \Log::error([
                'Error' => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine(),
            ]);
            abort(404);
        }


    }
}
