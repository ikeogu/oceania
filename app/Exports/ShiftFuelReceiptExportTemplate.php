<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PhpOffice\PhpSpreadsheet\Cell\DataType as PhpSpreadsheetDataType;
use Log;


class ShiftFuelReceiptExportTemplate
extends DefaultValueBinder
implements
    FromView,
    WithHeadings,
    WithEvents,
    WithCustomValueBinder,
    ShouldAutoSize,
    WithColumnFormatting
{
    protected $staff_id;
    protected $start;
    protected $stop;
    protected $title;
    protected $nshift_id;
    protected $tableHeaderLength = 17;
    protected $nshift_size = 0;
    protected $shift_count;

    public function __construct($staff_id, $start, $stop, $title, $nshift_id, $shift_count)
    {
        $this->staff_id = $staff_id;
        $this->start = $start;
        $this->stop = $stop;
        $this->title = $title;
        $this->nshift_id = $nshift_id;
        $this->shift_count = $shift_count;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        Log::info('PF **** start view() *****');

        /* $start = date('Y-m-d 00:00:00', strtotime($this->start));
        if (empty($this->stop)) {
            $stop =  date('Y-m-d H:i:s');
        } else {
            $stop = date('Y-m-d 23:59:59', strtotime($this->stop));
        } */

        $nshift = DB::table('nshift')->select('nshift.*')->where('id', $this->nshift_id)->first();

        // $out = empty($nshift->out) ? $stop : $nshift->out;

        $start = $this->start;
        $stop = $this->stop;

        $cstore_query = "
            SELECT
                SUM(crd.total) as cstore
            FROM
                cstore_receipt cr,
                cstore_receiptdetails crd
            WHERE
                crd.receipt_id = cr.id AND
                cr.staff_user_id  = $this->staff_id AND
                cr.created_at between '$start' and '$stop'
            ;
        ";

        $nshift->cstore = collect(DB::select(DB::raw($cstore_query)))->first()->cstore;
        Log::info('view: nshift->in = ' . json_encode($nshift->in));
        Log::info('view: out = ' . json_encode($stop));
        Log::info('view: nshift->cstore = ' . json_encode($nshift->cstore));

        DB::table('nshift')->whereId($nshift->id)->update(['cstore' => $nshift->cstore]);

        if (empty($nshift->out)) {
            $query = "
                SELECT
                    fr.id,
                    u.systemid as staff_id,
                    u.fullname as staff_name,
                    fr.systemid as receipt_i,
                    frl.fuel_receipt_tstamp as date,
                    frl.pump_no as pump_no,
                    frl.fuel as fuel,
                    frl.filled as filled,
                    frl.refund as refund,
                    frl.newsales_item_amount as newsales_item_amount,
                    frl.newsales_rounding as newsales_rounding,
                    fr.status as status,
                    frp.quantity as quantity,
                    frp.price as price,
                    frp.name as product_name,
                    frd.*
                FROM
                    fuel_receipt fr,
                    fuel_receiptlist frl,
                    fuel_receiptdetails frd,
                    fuel_receiptproduct frp,
                    users u
                WHERE
                    frl.fuel_receipt_id = fr.id AND
                    frd.receipt_id = fr.id AND
                    frp.receipt_id = fr.id  AND
                    frl.deleted_at IS NULL AND
                    fr.created_at  between '" . $start . "' and '" . $stop . "' AND
                    fr.staff_user_id = u.id AND
                    u.systemid = '$nshift->staff_systemid'
				ORDER BY
					fr.created_at DESC
            ;";

            //Log::debug("view: query=" . $query);

        } else {
            $query = "
                SELECT
                    fr.id,
                    u.systemid as staff_id,
                    u.fullname as staff_name,
                    fr.systemid as receipt_i,
                    frl.fuel_receipt_tstamp as date,
                    frl.pump_no as pump_no,
                    frl.fuel as fuel,
                    frl.filled as filled,
                    frl.refund as refund,
                    frl.newsales_item_amount as newsales_item_amount,
                    frl.newsales_rounding as newsales_rounding,
                    fr.status as status,
                    frp.quantity as quantity,
                    frp.price as price,
                    frp.name as product_name,
                    frd.*
                FROM
                    fuel_receipt fr,
                    fuel_receiptlist frl,
                    fuel_receiptdetails frd,
                    fuel_receiptproduct frp,
                    users u
                WHERE
                    frl.fuel_receipt_id = fr.id AND
                    frd.receipt_id = fr.id AND
                    frp.receipt_id = fr.id  AND
                    frl.deleted_at IS NULL AND
                    frl.created_at between '$start' and '$stop' AND
                    fr.staff_user_id = u.id AND
                    u.systemid = '$nshift->staff_systemid'
				ORDER BY
					fr.created_at DESC
                ;
            ";

            // Log::debug("View : query-->" .$query);
        }

        $data = collect(DB::select(DB::raw($query)));
        $products = DB::table('fuel_receiptproduct')->select('name')->groupBy('name')->get();

        $this->tableHeaderLength += count($products) * 2;

        $nshift->in = (!isset($nshift->in)) ? '' :
            date('dMy H:i:s', strtotime($nshift->in));

        $nshift->out = (!isset($nshift->out)) ? '' :
            date('dMy H:i:s', strtotime($nshift->out));

        $cash = '=V' . (8 + $data->count());
        Log::info('PF view: data=' . count($data));
        Log::info('PF view: products=' . count($products));

        Log::info('PF **** end view() *****');

        return view('excel_export.shift_fuelreceipt_template', [
            'receiptList' => $data,
            'nshift' => [$nshift],
            'product_name' => $products,
            'start_date' => $start,
            'stop_date' => $stop,
            'title' => $this->title,
            'nshift_count' => $this->shift_count,
            'cash' => $cash
        ]);
    }
    /**
     * @return array
     */

    /**
     * Write code on Method
     *
     * @return response()
     */

    public function headings(): array
    {
        return [];
    }

    /*   public function title(): string
    {
        return '';
    } */

    public function columnFormats(): array
    {
        return [
            'E' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'F' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'H' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'I' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'J' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'K' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'L' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'M' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'N' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'O' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'P' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Q' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'R' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'S' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'T' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'U' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'W' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'X' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Y' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
            'Z' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,

        ];
    }

    /**
     * Write code on Method
     *
     * @return response()
     */
    public function registerEvents(): array
    {
        return [

            AfterSheet::class => function (AfterSheet $event) {
                // 1
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(18);
            },
        ];
    }

    public function bindValue(Cell $cell, $value)
    {

        if (is_numeric($value) && strlen($value) > 15 || strlen(trim($value, ' ')) > 10) {

            $cell->setValueExplicit($value, PhpSpreadsheetDataType::TYPE_STRING);
            return true;
        }
        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public static function columnLetter($c)
    {

        $c = intval($c);
        if ($c <= 0) {
            return '';
        }

        $letter = '';

        while ($c != 0) {
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }

        return 'A7:' . $letter . '7';
    }

    public static function columnHeader($c)
    {

        $c = intval($c);
        if ($c <= 0) {
            return '';
        }

        $letter = '';

        while ($c != 0) {
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }

        return 'A2:' . $letter . '2';
    }
}
