<?php

namespace App\Imports;

use DB;
use Log;
use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;

class ExcelExtractor implements ToCollection
{

    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }

    /**
     * @param Collection $collection
     */
    public function collection(Collection $collection)
    {
        Log::info("***** Collection: Start *****");


        $rowHeaders = collect();
        $rowValues = collect();
        foreach ($collection as $c) {
            # code...
            if ($c[2] == "Date") {
                $rowHeaders->push($c);
            }

            if (strlen($c[5]) > 10) {
                $rowValues->push($c);
            }
        }

        $new_data = collect();
        $rowHeaders = $rowHeaders->first();

        foreach ($rowValues as  $item) {
            $c = 0;
            $data = [];
            foreach ($rowHeaders as $rh) {

                //Log::info('rh='.$rh.', item['.$c.']='.$item[$c]);

                $data += [$rh => $item[$c]];
                $c++;
            }

            $new_data->push($data);
        }

        //Log::info('1. new_data='.json_encode($new_data));

        $p1 = 'Ron95';
        $p2 = 'Ron97';
        $p3 = 'Diesel B20';
        $p4 = 'Diesel B7';
        $product_name = array($p1, $p2, $p3, $p4);

        $new_array = array(
            $p1 => 0, $p1 . ' Qty' => 0,
            $p2 => 0, $p2 . ' Qty' => 0,
            $p3 => 0, $p3 . ' Qty' => 0,
            $p4 => 0, $p4 . ' Qty' => 0
        );

        foreach ($new_data as $d) {

            foreach ($product_name as $p) {
                $p = trim($p);

                if (!empty($new_array) && !empty($d[$p])) {
                    $d[$p] = trim($d[$p]);

                    $new_array[$p] += $d[$p];

                    Log::info('Found new ' . $p . '=' . $d[$p]);
                    Log::info('Summed new_array[' . $p . ']=' . $new_array[$p]);

                    if (!empty($d[$p . ' Qty'])) {
                        $new_array[$p . ' Qty'] += $d[$p . ' Qty'];

                        Log::info('Found new ' . $p . ' Qty=' . $d[$p . ' Qty']);
                        Log::info('Summed new_array[' . $p . ' Qty]=' . $new_array[$p . ' Qty']);
                    }
                } elseif (!empty($d[$p]) && !empty($d[$p . ' Qty'])) {
                    array_push($new_array, [$d[$p], $d[$p . ' Qty']]);
                }

                //Log::info('2. new_array='.json_encode($new_array));
            }
            //Log::info('3. new_array='.json_encode($new_array));
        }
        // try {
        $parent_exists = DB::table('fuelsales_summary')->
        where('start', date(
            'Y-m-d 00:00:00',
            strtotime($this->request->fuel_start_date)
        ))->
        where('end', date(
                'Y-m-d 23:59:59',
                strtotime($this->request->fuel_end_date)
            ))->
        first();

        if (empty($parent_exists)) {

            $parentId =  DB::table('fuelsales_summary')->
            insertGetId([
                'start' => date(
                    'Y-m-d H:i:s',
                    strtotime($this->request->fuel_start_date)
                ),
                'end' => date(
                    'Y-m-d 23:59:59',
                    strtotime($this->request->fuel_end_date)
                ),
                'user_id' => 0,
                'created_at' => date('Y-m-d H:i:s'),
                'updated_at' => date('Y-m-d H:i:s')
            ]);
        } else {
            DB::table('fuelsales_summary')->
            whereId($parent_exists->id)->
            update([
                    'updated_at' => date('Y-m-d H:i:s')
            ]);
        }

        $parentId = empty($parent_exists) ? $parentId : $parent_exists->id;

        Log::info('parentId=' . json_encode($parentId));
        foreach ($new_array as $key => $v) {
            // dd($new_array);
            foreach ($product_name as $p) {
                if ($p == $key && $key != null) {

                    // try {
                        // Testing existence of record
                        $sales =  $new_array[$key] * 100;
                        $qty = $new_array[$key . ' Qty'] * 100;

                        if (empty($parent_exists)) {

                        // dump('first', $sales, $qty);
                            Log::info("7. new_array[$key]=" . $new_array[$key]);
                            Log::info("8. new_array[$key Qty]=" . $new_array[$key . ' Qty']);

                            DB::table('fuelsales_summary_detail')->
                            insert([
                                'fuelsales_summary_id' => $parentId,
                                'product_name' => $p,
                                'qty' => $qty,
                                'sales' =>$sales,
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        } else {
                            // Record exists in fuelsales_summary because daterange is
                            // the same. Need to update fuelsales_summary_detail!

                            // dump('else', $sales,$qty);
                            $ret = DB::table('fuelsales_summary_detail')->
                            where('fuelsales_summary_id', $parentId)->
                            where('product_name', $p)->
                            update([
                                'qty' => $qty,
                                'sales' => $sales,
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);

                            Log::warning('-------------------------------------');
                            Log::warning('UPDATE! ret=' . json_encode($ret));
                            // Log::warning('UPDATE! parentId=' . $parentId);
                            Log::warning('UPDATE! product_name=' . $p);
                            Log::warning('UPDATE! qty=' . $new_array[$key . ' Qty'] * 100);
                            Log::warning('UPDATE! sales=' . $new_array[$key] * 100);
                            Log::warning('UPDATE! updated_at=' . date('Y-m-d H:i:s'));
                            Log::warning('-------------------------------------');

                            //die();
                        }
                   /*  } catch (\Exception $e) {
                        //dump('Error: fuelsales_summary_details write failed!');
                        \Log::error([
                            "Mesg"   => $e->getMessage(),
                            "File"  => $e->getFile(),
                            "Line"  => $e->getLine()
                        ]);
                    } */
                }
            }
        }




    }
}
