<?php

namespace App\Exports;

use Directory;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithPreCalculateFormulas;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType as PhpSpreadsheetDataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use PDF;
use Log;
use Illuminate\Support\Facades\Storage;


class MainFuelReceiptListExport
    extends DefaultValueBinder
    implements FromView, WithColumnFormatting, WithHeadings, WithTitle, WithEvents,
        WithCustomValueBinder, ShouldAutoSize, WithCalculatedFormulas, WithPreCalculateFormulas
{
    protected $request;
    protected $tableHeaderLength = 17;
    protected $nshift_size = 0;
    private $len = 6;
    protected $title;
    protected $directory = '';

    public function __construct($request,$title, $directory)
    {
        $this->request = $request;
        $this->title = $title;
        $this->directory = $directory;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {
        Log::info('PF ***** main: start *****');

        // dd($this->request->all());
        // $requestValue = $this->request->all();

        if (
            !isset($this->request->fuel_start_date) &&
            !isset($this->request->fuel_stop_date)
        ) {
            $start = date("Y-m-d", time());
            $stop = date("Y-m-d", time());
        } else {
            $start = date('Y-m-d', strtotime($this->request->fuel_start_date));
            $stop = date('Y-m-d', strtotime($this->request->fuel_end_date));
        }

        $nshift = DB::table('nshift')->select('nshift.*');

        $nshift = $nshift->whereBetween(
            'nshift.created_at',
            [$start . ' 00:00:00', $stop . ' 23:59:59']
        );

        $nshift = $nshift->get()->map(function ($d) {
            $d->in = (!isset($d->in)) ? '' : date(
                'dMy H:i:s',
                strtotime($d->in)
            );

            $d->out = (!isset($d->out)) ? '' : date(
                'dMy H:i:s',
                strtotime($d->out)
            );
            return $d;
        });

        $first_record_in = null;
        $last_record_out = null;

        if (sizeof($nshift) > 0) {
            $first_record_in = $nshift[0]->in;
            $last_record_out = $nshift[sizeof($nshift) - 1]->out;

            if (
                is_null($last_record_out) ||
                $last_record_out == "" ||
                empty($first_record_in)
            ) {
                $last_record_out = date("Y-m-d", time()) . ' 23:59:59';
            }
        }
        /* This fetches only the current day's data
		else {
            $first_record_in = date("Y-m-d", time()) . ' 00:00:00';
            $last_record_out = date("Y-m-d", time()) . ' 23:59:59';
        }
		*/

        $start = date('Y-m-d', strtotime($this->request->fuel_start_date));
        $stop = date('Y-m-d', strtotime($this->request->fuel_end_date));

        $data = collect(DB::select(DB::raw(
            "
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
                frp.product_id as product_id,
                frp.name as product_name,
                frd.item_amount as sales_amt,
                frp.quantity as sales_qty,
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
                u.id = fr.staff_user_id AND
                frl.created_at between '".$start." 00:00:00' AND '".$stop." 23:59:59'
            ORDER BY
                fr.created_at DESC
            ;
         "
        )));

        $products = DB::table('fuel_receiptproduct')->select('name')->groupBy('name')->get();

         $this->save_fuel_sale_summary_pdf($data,$start,$stop, $this->request->directory);

        Log::info("PF main: data=" . count($data));
        Log::info('PF main: products=' . count($products));

        // dd($products);
        $this->len += sizeof($data) + sizeof($nshift);

        $this->tableHeaderLength += count($products) * 2;

        Log::info('PF main: this->len=' . $this->len);

        // dd($this->len);
        // static::download_fuel_sale_summary($data,$start,$stop);
        Log::info('PF ***** main: end *****');

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

            $cell->setValueExplicit($value, PhpSpreadsheetDataType::TYPE_STRING);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
    }

    public static function  save_fuel_sale_summary_pdf($data,$start,$stop,$directory)
    {
        $collect = collect();
       ;
        foreach ($data as $key => $p) {
            # code...
            if($collect->contains('product_id',$p->product_id) && $p->status != 'voided'){
                $collect->where('product_id',$p->product_id)->
                    first()->sales_amt +=$p->sales_amt;
                $collect->where('product_id', $p->product_id)->
                    first()->sales_qty += $p->sales_qty;
            }else{
                if($p->status != 'voided'){
                    $collect->push($p);
                }

            }
        }
        $receipt_sld = $collect;

        $pdf = PDF::loadView('excel_export.fuel_sales_summary_pdf', compact(
            'receipt_sld',
            'start',
            'stop'
        ))->setPaper('A4', 'portrait');

        $pdf->save(Storage::disk('local')->put(
            '' .$directory . '/' .
            $start . '-' . $stop . 'fuel_sales_summary.pdf',
            $pdf->output()
        ));
    }

}
