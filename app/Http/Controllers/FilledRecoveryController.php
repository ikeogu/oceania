<?php

namespace App\Http\Controllers;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Log;
use Yajra\DataTables\DataTables;

class FilledRecoveryController extends Controller
{

    public function index(Request $request)
    {
        $date = $request->date;
        // $this->copyInactiveToRefill($date);

        return view('fuel_receipt.fuel_recall', compact('date'));
    }

    public function updatePumpRecord(Request $request)
    {
        $status = false;
        $pump_no = $request->pump_no;
        $data = $request->only(['filled', 'nozzle', 'price', 'trans_no', 'volume', 'type']);
        $last = DB::table('fuel_receiptlist')
            ->where('pump_no', $pump_no)
            ->max('id');

        if (!empty($last)) {
            $receiptlist = DB::table('fuel_receiptlist')
                ->where('id', $last)
                ->first();
            $data['refund_qty'] = $receiptlist->refund_qty;

            $status = true;
            $record = DB::table('fuel_receiptlist')->find($last);

            $data['price'] = $data['price'] * 100;
            $data['filled'] = $data['filled'] * 100;
            $data['refund_amt'] = $record->fuel - $data['filled'];

            $isFilledRecoveryExists = DB::table('filled_recovery')
                ->where('receipt_id', $last)
                ->where('pump_no', $pump_no)
                ->get();

            if (sizeof($isFilledRecoveryExists) > 0) {
                DB::table('filled_recovery')
                    ->where('receipt_id', $last)
                    ->where('pump_no', $pump_no)
                    ->update($data);
            } else {
                $data['pump_no'] = $pump_no;
                $data['receipt_id'] = $last;
                $data['date'] = $record->created_at;
                $data['fuel'] = $record->fuel;
                $data['created_at'] = now();
                $data['updated_at'] = now();
                DB::table('filled_recovery')->insert($data);
            }
        }

        return response()->json(["update" => $status, "msg" => ""]);
    }

    private function recordExists($pump_no, $receipt_id, $date)
    {
        $record = DB::table('filled_recovery')->where('pump_no', '=', $pump_no)
            ->where('filled_recovery.receipt_id', $receipt_id)
            ->where('filled_recovery.date', 'LIKE', '%' . date('Y-m-d', strtotime($date)) . '%')
            ->get()
            ->first();
        if (!is_null($record)) {
            return true;
        } else {
            return false;
        }
    }

    private function copyInactiveToRefill($date)
    {
        $data = DB::table('fuel_receipt')->join('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', 'fuel_receipt.id')->selectRaw('fuel_receipt.status,
			fuel_receiptlist.id,
			fuel_receiptlist.fuel_receipt_systemid,
			fuel_receiptlist.total,
			fuel_receiptlist.filled,
			fuel_receiptlist.refund,
			fuel_receiptlist.pump_no,
			fuel_receiptlist.fuel_receipt_id AS receipt_id,
			fuel_receiptlist.created_at AS created_at')->whereNull('fuel_receiptlist.deleted_at')
        #->whereDate('fuel_receiptlist.created_at', date('Y-m-d', strtotime($date)))
            ->where('fuel_receiptlist.status', '!=', 'active')
            ->orderBy('fuel_receiptlist.id', 'DESC')
            ->get();

        foreach ($data as $dt) {
            $isRecordExists = $this->recordExists($dt->pump_no, $dt->receipt_id, $dt->created_at);
            if (!$isRecordExists) {
                DB::table('filled_recovery')->insert([
                    'pump_no' => $dt->pump_no,
                    'date' => $dt->created_at,
                    'receipt_id' => $dt->receipt_id,
                    'fuel' => $dt->total,
                    'filled' => $dt->refund,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]);
            }
        }

    }

    private function padRecords($data)
    {
        $result = [];
        $len = sizeof($data);
        $rem = 20 - $len;

        $keys = [
            "created_at",
            "date",
            "filled",
            "fuel_receipt_systemid",
            "id",
            "pump_no",
            "receipt_id",
            "refund",
            "status",
            "total",
        ];

        foreach ($data as $dt) {
            array_push($result, $dt);
        }

        $template_list = [];
        for ($i = 0; $i < 20; $i++) {
            $empty_object = [];
            foreach ($keys as $key) {
                if ($key == 'pump_no') {
                    $empty_object[$key] = $i + 1;
                } else {
                    $empty_object[$key] = '0';
                }
            }
            array_push($template_list, (object) $empty_object);
        }

        for ($i = 0; $i < sizeof($result); $i++) {
            // $ele = (object) $result[$i]->toArray();
            $ele = (object) $result[$i];

            $pump_number = (int) $ele->pump_no;
            $template_list[$pump_number - 1] = $ele;
        }

        return $template_list;
    }

    public function datatableFilledRecovery(Request $request)
    {
        $data = [];
        $date = $request->date;

        // Log::debug('datatableFilledRecovery: $date = ' . $date);

        // $pumps = DB::table('fuel_receiptlist')
        //     ->selectRaw('MAX(id) AS max')
        // //->where('fuel_receiptlist.created_at', 'LIKE', '%' . date('Y-m-d', strtotime($date)) . '%')
        //     ->groupBy('fuel_receiptlist.pump_no')
        //     ->get();
        // Log::debug('datatableFilledRecovery: $pumps = ' . json_encode($pumps));

        // if (sizeof($pumps) > 0) {
        //     $filtered_pumps = [];
        //     foreach ($pumps as $pump) {
        //         array_push($filtered_pumps, $pump->max);
        //     }
        //     $pumps = $filtered_pumps;
        // }

        // $receipt_lists = DB::table('fuel_receiptlist')
        //     ->selectRaw('fuel_receiptlist.total,
        //         fuel_receiptlist.filled,
        //         fuel_receiptlist.refund,
        //         fuel_receiptlist.pump_no,
        //         fuel_receiptlist.fuel_receipt_systemid,
        //         fuel_receiptlist.total,
        //         fuel_receiptlist.id,
        //         fuel_receiptlist.id as receipt_id,
        //         fuel_receiptlist.created_at,
        //         fuel_receiptlist.status')
        //     ->where('fuel_receiptlist.status', '!=', 'active')
        //     ->whereIn('fuel_receiptlist.id', $pumps)
        //     ->orderBy('fuel_receiptlist.id', 'DESC')
        //     ->limit(20)
        //     ->get();

        // Log::debug('datatableFilledRecovery: $receipt_lists = ' . json_encode($receipt_lists));

        // $filled_recovery = DB::table('filled_recovery')->get();

        // if (sizeof($filled_recovery) == sizeof($receipt_lists)) {
        // $data = DB::table('fuel_receiptlist')
        //     ->join('filled_recovery', 'filled_recovery.receipt_id', 'fuel_receiptlist.id')
        //     ->selectRaw('fuel_receiptlist.total,
        //         filled_recovery.filled,
        //         filled_recovery.fuel as fuel,
        //         fuel_receiptlist.refund,
        //         fuel_receiptlist.pump_no,
        //         fuel_receiptlist.fuel_receipt_systemid,
        //         fuel_receiptlist.total,
        //         fuel_receiptlist.id,
        //         fuel_receiptlist.id as receipt_id,
        //         fuel_receiptlist.created_at,
        //         fuel_receiptlist.status')
        //     /*->where('fuel_receiptlist.status', '!=', 'active')*/
        //         ->whereIn('fuel_receiptlist.id', $pumps)
        //         ->orderBy('fuel_receiptlist.id', 'DESC')
        //         ->limit(20)
        //         ->get();

        // } else {
        //     $data = $receipt_lists;
        // }

        $data = DB::table('fuel_receiptlist')
            ->join('filled_recovery', 'filled_recovery.receipt_id', 'fuel_receiptlist.id')
            ->selectRaw('fuel_receiptlist.total,
                filled_recovery.filled,
                filled_recovery.fuel as fuel,
                fuel_receiptlist.pump_no,
                fuel_receiptlist.refund,
                fuel_receiptlist.fuel_receipt_systemid,
                fuel_receiptlist.total,
                fuel_receiptlist.id,
                fuel_receiptlist.id as receipt_id,
                fuel_receiptlist.created_at,
                fuel_receiptlist.status')
            ->get();

        if (sizeof($data) < 20) {
            $data = $this->padRecords($data);
        }

        return Datatables::of($data)->setRowId(function ($data) {
            Log::debug('datatableFilledRecovery: $data = ' . json_encode($data));
            return 'pump_receipt_data_' . $data->pump_no . '-' . $data->id;
        })->addIndexColumn()->addColumn('pump_no', function ($data) {
            $pump_no = ($data->pump_no == "") ? "" : $data->pump_no;
            return <<<EOD
                    $pump_no
EOD;
        })->addColumn('date', function ($data) {
            $created_at = ($data->created_at == "0") ? "" : Carbon::parse($data->created_at)->format('dMy H:i:s');
            return <<<EOD
                    $created_at
EOD;
        })->addColumn('fuel_receipt_systemid', function ($data) {
            $receipt_id = ($data->fuel_receipt_systemid == "0") ? '' : '<a href="javascript:void(0)"  style="text-decoration:none;" onclick="getFuelReceiptlist(' . $data->receipt_id . ')" > ' . $data->fuel_receipt_systemid . '</a>';
            return <<<EOD
                $receipt_id
EOD;
        })->addColumn('total', function ($data) {
            if ($data->status == "") {
                $total = "";
            } else {
                if ($data->status === "voided") {
                    $total = '0.00';
                } else {
                    $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
                }
            }
            return <<<EOD
                $total
EOD;
        })->addColumn('fuel', function ($data) {
            $total = "";
            if ($data->total != "0") {
                $total = !empty($data->total) ? number_format($data->total / 100, 2) : '0.00';
            }

            return <<<EOD
                $total
EOD;
        })->addColumn('filled', function ($data) {
            $filled = "";
            if ($data->filled != "0") {
                $filled = !empty($data->total) ? number_format($data->filled / 100, 2) : '0.00';
            }

            return <<<EOD
                $filled
EOD;
        })->addColumn('status_color', ' ')->editColumn('status_color', function ($row) {
            $status = "none";
            if ($row->status === "voided") {
                $status = "red";
            }
            if ($row->status === "refunded") {
                $status = "#ff7e30";
            }

            return $status;
        })->addColumn('refund', function ($data) {
            $refund = 0;
            if (!is_null($data->refund)) {
                $refund = $data->refund;
            }

            return <<<EOD
                $refund
EOD;
        })->addColumn('action', function ($row) {
            $refund = ($row->total / 100) - ($row->filled / 100);
            if ($row->status != "refunded" && $refund > 0 && $row->status != "voided" && $row->filled != "0.00") {
                $btn = '<a  href="javascript:void(0)"  onclick="refundMe(' . $row->receipt_id . ', ' . $row->total / 100 . ', ' . $row->filled / 100 . ')" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/pinkcrab_50x50.png") . '" alt=""> </a>';
                return $btn;
            } else {
                $btn = '<a  href="javascript:void(0)" onClick="recallBtn(' . $row->pump_no . ')"  style=" filter: background: red; /*pointer-events: none; */cursor: pointer;" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/pinkcrab_50x50.png") . '"" alt=""> </a>';
                /*
                if ($row->id != "0") {
                $btn = '<a  href="javascript:void(0)" onClick="recallBtn(' . $row->pump_no . ')"  style=" filter: background: red; pointer-events: none; cursor: pointer;" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/pinkcrab_50x50.png") . '"" alt=""> </a>';
                } else {
                $btn = '<a  href="javascript:void(0)" disabled="disabled"  style=" filter: background: red; pointer-events: none; cursor: pointer;" data-row="' . $row->id . '" class="delete"> <img width="25px" src="' . asset("images/pinkcrab_50x50.png") . '"" alt=""> </a>';
                } */

                return $btn;
            }
        })
            ->escapeColumns([])->make(true);
    }
}
