<?php

namespace App\Imports;

use Illuminate\Support\Collection;
use Maatwebsite\Excel\Concerns\ToCollection;
use Log;
use Illuminate\Support\Facades\DB;

class NshiftExcelExtractor implements ToCollection
{
    public $data;

    public function __construct($data)
    {
        $this->data = $data;
    }
    /**
    * @param Collection $collection
    */
    public function collection(Collection $collection)
    {
        //
    
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

        Log::info('4. new_array=' . json_encode($new_array));

        $out = empty($this->data->out) ? date('Y-m-d 23:59:59') : $this->data->out;
        try {
            $parent_exists = DB::table('nshift_fuelsales_summary')->
                where('shift_in', $this->data->in)->
                where('shift_out',$out)->
                where('shift_no',$this->data->id)->
                first();

            if (empty($parent_exists)) {

                $parentId =  DB::table('nshift_fuelsales_summary')->
                insertGetId([
                    'shift_in' => $this->data->in,
                    'shift_out' => $out,
                    'shift_no' => $this->data->id,
                    'staff_id_systemid' => $this->data->staff_systemid,
                    'staff_name' => $this->data->staff_name,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            } else {

                DB::table('nshift_fuelsales_summary')->
                where('shift_in', $this->data->in)->
                where('shift_out', $out)->
                where('shift_no', $this->data->id)->
                update([
                    'updated_at' => date('Y-m-d H:i:s')
                ]);
            }
            $parentId = empty($parent_exists) ? $parentId : $parent_exists->id;

            // dump($parentId);

            foreach ($new_array as $key => $v) {
			foreach ($product_name as $p) {
				if ($p == $key && $key != null) {

				try {
					// Testing existence of record
					if (empty($parent_exists)) {
						Log::info("7. new_array[$key]=" . $new_array[$key]);
						Log::info("8. new_array[$key Qty]=" . $new_array[$key . ' Qty']);

						DB::table('nshift_fuelsales_summary_detail')->
						insert([
							'nshift_fuelsales_summary_id' => $parentId,
							'product_name' => $p,
							'qty' => $new_array[$key . ' Qty'] * 100,
							'sales' => $new_array[$key] * 100,
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s')
						]);
					} else {
						// Record exists in fuelsales_summary because daterange is
						// the same. Need to update fuelsales_summary_detail!

						$ret = DB::table('nshift_fuelsales_summary_detail')->
						where('nshift_fuelsales_summary_id', $parentId)->
						where('product_name', $p)->
						update([
							'qty' => $new_array[$key . ' Qty'] * 100,
							'sales' => $new_array[$key] * 100,
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
				} catch (\Exception $e) {
					//dump('Error: fuelsales_summary_details write failed!');
					\Log::error([
						"Mesg"   => $e->getMessage(),
						"File"  => $e->getFile(),
						"Line"  => $e->getLine()
					]);
				}

				} else {
					//Log::info('ELSE p='.$p.', key='.$key);
				}
			}}
        } catch (\Exception $e) {
            //dump('Error: fuelsales_summary write failed!');
            \Log::error([
                "Mesg"   => $e->getMessage(),
                "File"  => $e->getFile(),
                "Line"  => $e->getLine()
            ]);
            return -1;
        }
    }
}
