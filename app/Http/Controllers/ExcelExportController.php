<?php

namespace App\Http\Controllers;

use App\Exports\AuditedReportExport;
use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;
use App\Exports\CstoreExport;
use App\Exports\FuelFullTankReceiptExport;
use App\Exports\FuelReceiptListExport;
use App\Exports\ReceivingNoteExport;
use App\Exports\ReturningNoteExport;
use App\Exports\ShiftFuelReceiptExportTemplate;
use App\Exports\StockLedgerExport;
use Illuminate\Http\Request;
use Maatwebsite\Excel\Facades\Excel;
use DB;
use PDF;
use Log;

class ExcelExportController extends Controller
{
    //
    public function exportToExcelFuelReceiptlist(Request $request)
    {

        $excel = new FuelReceiptListExport($request);
        $data = Excel::download($excel, 'fuel_receipt_list.xlsx', \Maatwebsite\Excel\Excel::XLSX) ;
        return $data;
    }

    public static function saveExcelFuelReceiptlist(Request $request)
    {

        $excel = new FuelReceiptListExport($request);
         $data =  Excel::store($excel,$request->fuel_start_date.'-'.
            $request->fuel_stop_date.'fuel_receipt_list.xlsx');
        ob_end_clean();
        return $data;
    }
    public static function saveExcelNshift($staff_id,$start,$stop,$title,$nid,$counter)
    {

        $excel = new ShiftFuelReceiptExportTemplate($staff_id, $start, $stop, $title, $nid, $counter);
        $data =  Excel::store($excel, $start . '-' .
            $staff_id . 'nshift_fuel_receipt_list.xlsx');
        ob_end_clean();
        return $data;
    }



    public function exportToExcelFuelFullTankReceiptList(Request $request)
    {
        $data = Excel::download(new FuelFullTankReceiptExport($request),
            'fuel_fulltank_receipt_list.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();
        return $data;
    }

    public function exportToExcelNshift(Request $request)
    {
        $request->nshift = true;
        $data = Excel::download(new FuelReceiptListExport($request),
            'fuel_receipt_list.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();
        return $data;
    }

    public function exportCstore(Request $request)
    {
        $data = Excel::download(new CstoreExport($request),
            'cstore_receipt_list.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();
        return $data;
    }

    public function exportStockLedger(Request $request)
    {
        $data = Excel::download(new StockLedgerExport($request),
            'stock_ledger_excel.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        ob_end_clean();
        return $data;
    }

    public function exportReturningNote(Request $request)
    {
        $excel = new ReturningNoteExport($request);
        $data = Excel::download($excel, 'returning_note.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        return $data;
    }

    public function exportReceivingNote(Request $request)
    {
        $excel = new ReceivingNoteExport($request);
        $data = Excel::download($excel, 'receiving_note.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        return $data;
    }
    public function exportAuditedReport(Request $request)
    {
        $excel = new AuditedReportExport($request);
        $data = Excel::download($excel, 'audited_report.xlsx', \Maatwebsite\Excel\Excel::XLSX);
        return $data;
    }


    public static function cstore_Ntd_generator($arrays, $status, $len)
    {
        $front = '';
        $cnt = 1;
        $keys = array_keys($arrays);

        for ($f = 0; $f <= $len - 1; $f++) {

            if (array_key_exists($f, $arrays)) {

                if ($status == 'voided') {
                    $front .= "<td style='text-align:right;' data-format='0.00'>"
                    . 0 . "</td>\n";
                } else {
                    $front .= "<td style='text-align:right;' data-format='0.00'>"
                    . intval($arrays[$f]) / 100 . "</td>\n";
                }

            } else {
                $front .= '<td> </td>';
            }
        }

        echo $front;
    }

    public static function tdgenerator($position, $value, $len, $status)
    {
        $front = '';
        for ($f = 0; $f <= $position; $f++) {

            if ($f == $position) {
                if ($status == 'voided') {
                    $front .= "<td style='text-align:right;' data-format='0.00'>" .
                    0 . "</td>\n";
                } else {
                    $front .= "<td style='text-align:right;' data-format='0.00'>" .
                    $value / 100 . "</td>\n";
                }
            } else {
                # code...
                $front .= "<td> </td>\n";
            }
        }

        for ($i = $position + 1; $i <= $len - 1; $i++) {
            $front .= "  <td> </td>\n";
        }

        echo $front;
    }

    public static function fuel_fulltank_tdgenerator($position, $value, $len)
    {
        $front = '';
        for ($f = 0; $f <= $position; $f++) {

            if ($f == $position) {
                $front .= "<td style='text-align: right;'>"
                . $value / 100 . "</td>\n";
            } else {
                # code...
                $front .= "  <td> </td>\n";
            }
        }
        for ($i = $position + 1; $i <= $len - 1; $i++) {
            $front .= "  <td> </td>\n";
        }
        echo $front;
    }

    public static function generateColumnAZserial($start_letter, $size)
    {
        $start = false;
        $result = [];
        for ($i = 'A'; $i <= 'Z'; $i++) {
            if ($start_letter == $i) {
                $start = true;
            }

            if ($start == true && $size > 0) {
                array_push($result, $i);
                $size = $size - 1;
            }
        }

        $next = $start_letter;

        if (sizeof($result) > 0) {
            $last_letter = $result[sizeof($result) - 1];
            $next = ++$last_letter;
        }

        return (object) [
            'result' => $result,
            'next' => $next,
        ];
    }

    public static function tdgenerator_qty($position, $value, $len, $object)
    {
        $front = '';
        for ($f = 0; $f <= $position; $f++) {

            if ($f == $position) {
                if ($object->status == 'refunded') {
                    if ($object->price > 0) {
                        $qty = $object->filled / $object->price;
                    } else {
                        $qty = -0;
                    }
                    $front .= "<td style='text-align: right;' data-format='0.00'>"
                    . round($qty,2) . "</td>\n";
                } elseif ($object->status == 'voided') {
                    $front .= "<td style='text-align: right;' data-format='0.00'>"
                    . 0 . "</td>\n";
                } else {
                    $front .= "<td style='text-align: right;' data-format='0.00'>"
                    . $value . "</td>\n";
                }
            } else {
                # code...
                $front .= "  <td> </td>\n";
            }
        }
        for ($i = $position + 1; $i <= $len - 1; $i++) {
            $front .= "  <td> </td>\n";
        }
        echo $front;
    }
    public static function getProductNamearray($product_name)
    {
        $newPrdList = [];
        foreach ($product_name as $key => $value) {
            # code...
            array_push($newPrdList, $value->name);
        }
        return $newPrdList;
    }

    public static function FulltankReceiptpaymentMethod($i, $method)
    {
        if ($method > 0 && $i->status == 'refunded') {
            return ($i->total + $i->rounding - $i->refund) / 100;
        } elseif ($i->status == 'voided' && $method > 0) {
            return 0;
        } elseif ($method > 0 && $i->rounding > 0) {
            return ($i->total + $i->rounding) / 100;
        } elseif ($method > 0) {
            return ($i->total) / 100;
        } elseif ($method > 0 && $i->status == 'refunded' && $i->rounding > 0) {
            return ($i->total + $i->rounding - $i->refund) / 100;
        }
    }

    public static function FuelReceiptpaymentMethod($i, $method)
    {
        if ($i->status == 'refunded' && $i->refund > 0 && $method > 0) {
            return ($i->filled + $i->newsales_rounding) / 100;
        } elseif ($i->status == 'voided' && $method > 0) {
            return 0;
        } elseif ($i->status != 'voided' && $method > 0 && $i->status != 'refunded') {
            return $i->total / 100;
        } elseif ($method > 0) {
            return $i->total / 100;
        }
    }

    public static function CstorepaymentMethod($i, $method)
    {
        if ($i->status == 'refunded' && $i->refund > 0 && $method > 0) {
            return ($i->total + $i->rounding - $i->refund - $i->change) / 100;
        } elseif ($i->status == 'voided' && $method > 0) {
            return 0;
        } elseif ($method > 0) {
            return $i->total / 100;
        }
    }

    public static function sum($start, $end){
         return "=SUM($start:$end)";


        // $objPHPExcel->getActiveSheet()->setCellValue('A20', '=AE19 * I19');

    }
}
