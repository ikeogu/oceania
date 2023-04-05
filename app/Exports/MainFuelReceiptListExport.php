<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
// use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;
// use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;


class MainFuelReceiptListExport extends DefaultValueBinder implements FromView, WithColumnFormatting, WithHeadings, WithTitle, WithEvents, WithCustomValueBinder, ShouldAutoSize
{
    protected $request;
    protected $tableHeaderLength = 17;
    protected $nshift_size = 0;
    protected $len = 6;
    protected $title;

    public function __construct($request,$title)
    {
        $this->request = $request;
        $this->title = $title;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        // dd($this->request->all());
        // $requestValue = $this->request->all();
        if (!isset($this->request->fuel_start_date) &&
            !isset($this->request->fuel_stop_date)) {
            $start = date("Y-m-d", time());
            $stop = date("Y-m-d", time());
        } else {
            $start = date('Y-m-d', strtotime($this->request->fuel_start_date));
            $stop = date('Y-m-d', strtotime($this->request->fuel_end_date));
        }

        $nshift = DB::table('nshift')
            ->select('nshift.*');
        $nshift = $nshift->whereBetween('nshift.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59']);
        $nshift = $nshift->get()->map(function ($d) {
            $d->in = (!isset($d->in)) ? '' : date('dMy H:i:s', strtotime($d->in));
            $d->out = (!isset($d->out)) ? '' : date('dMy H:i:s', strtotime($d->out));
            return $d;
        });

        $first_record_in = null;
        $last_record_out = null;
        if (sizeof($nshift) > 0) {
            $first_record_in = $nshift[0]->in;
            $last_record_out = $nshift[sizeof($nshift) - 1]->out;

            if (is_null($last_record_out) || $last_record_out == "" || empty($first_record_in)) {
                $last_record_out = date("Y-m-d", time()) . ' 23:59:59';
            }
        }
		/*
		else {
            $first_record_in = date("Y-m-d", time()) . ' 00:00:00';
            $last_record_out = date("Y-m-d", time()) . ' 23:59:59';
        }
		*/

        $start = date('Y-m-d', strtotime($this->request->fuel_start_date));
        $stop = date('Y-m-d', strtotime($this->request->fuel_end_date));


        $data = DB::table('fuel_receipt')->
            leftJoin('fuel_receiptdetails', 'fuel_receiptdetails.receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('users', 'users.id', '=', 'fuel_receipt.staff_user_id')->
            whereBetween('fuel_receiptlist.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])->
            select(
            'fuel_receipt.id',
            'users.systemid as staff_id',
            'users.fullname as staff_name',
            'fuel_receipt.systemid as receipt_i',
            'fuel_receiptlist.fuel_receipt_tstamp as date',
            'fuel_receiptlist.pump_no as pump_no',
            'fuel_receiptlist.fuel as fuel',
            'fuel_receiptlist.filled as filled',
            'fuel_receiptlist.refund as refund',
            'fuel_receiptlist.newsales_item_amount as newsales_item_amount',
            'fuel_receiptlist.newsales_rounding as newsales_rounding',
            'fuel_receipt.status as status',
            'fuel_receiptproduct.quantity as quantity',
            'fuel_receiptproduct.price as price',
            'fuel_receiptproduct.name as product_name',
            'fuel_receiptdetails.*'
        )->get();

        $products = DB::table('fuel_receiptproduct')
            ->select('name')
            ->groupBy('name')
            ->get();

        // dd($products);
        $this->len += sizeof($data) + sizeof($nshift);
        $this->tableHeaderLength += count($products) * 2;

        // dd($data);
        return view('excel_export.fuel_receiptlist_excel', [
            'receiptList' => $data,
            'nshift' => $nshift,
            'product_name' => $products,
            'start_date' => $start,
            'stop_date' => $stop,
        ]);
    }

    public function title(): string
    {
        return 'Main';
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
        return [
        ];
    }

    public function columnFormats(): array
    {
        return [
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
            'V' => NumberFormat::FORMAT_NUMBER_COMMA_SEPARATED1,
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
                $event->sheet->getDelegate()->
                    getRowDimension('1')->
                    setRowHeight(30);
            },
        ];
    }

    public function bindValue(Cell $cell, $value)
    {

        if (
            is_numeric($value) && strlen($value) > 15 ||
            strlen(trim($value, ' ')) > 11 &&
             strpos($value, '.') === false
        ) {

            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        // else return default behavior
        return parent::bindValue($cell, $value);
    }

}
