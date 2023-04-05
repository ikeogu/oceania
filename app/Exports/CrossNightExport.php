<?php


namespace App\Exports;

use Illuminate\Contracts\View\View;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithTitle;
use Maatwebsite\Excel\Events\AfterSheet;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;

class CrossNightExport extends DefaultValueBinder implements FromView, WithTitle, WithHeadings, WithEvents, WithCustomValueBinder, ShouldAutoSize
{

    protected $start;
    protected $stop;
    protected $tableHeaderLength = 17;
    protected $nshift_size = 0;

    public function __construct($start,$stop)
    {
        $this->start = $start;
        $this->stop = $stop;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {

        $nshift = DB::table('nshift')
            ->select('nshift.*');
        $nshift = $nshift->whereBetween('nshift.created_at', [$this->start, $this->stop]);
        $nshift = $nshift->get()->map(function ($d) {
            $d->in = (!isset($d->in)) ? '' : date('dMy H:i:s', strtotime($d->in));
            $d->out = (!isset($d->out)) ? '' : date('dMy H:i:s', strtotime($d->out));
            return $d;
        });

        $data_collection = collect();
            $data = DB::table('fuel_receipt')->
            leftJoin('fuel_receiptdetails', 'fuel_receiptdetails.receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('fuel_receiptlist', 'fuel_receiptlist.fuel_receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('fuel_receiptproduct', 'fuel_receiptproduct.receipt_id', '=', 'fuel_receipt.id')->
            leftJoin('users', 'users.id', '=', 'fuel_receipt.staff_user_id')->
            whereBetween('fuel_receiptlist.created_at', [$this->start, $this->stop])->
                // where('fuel_receiptlist.created_at','>=', $shift->in)->
            select(

                'fuel_receipt.id as receipt_id',
                'fuel_receipt.staff_user_id',
                'fuel_receipt.created_at as receipt_date',
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

        $data_collection = $data_collection->concat($data);
        $data_collection2 = collect();
        foreach($nshift as $shift) {
            $user = DB::table('users')
                ->where('systemid', $shift->staff_systemid)
                ->first();

            if($data_collection2->contains('staff_user_id', $user->id)) {
                continue;
            }
            $data_collection2= $data_collection->whereNotBetween('receipt_date', [$shift->in, $shift->out]);

        }

        // dd($data_collection);
        $products = DB::table('fuel_receiptproduct')->
            select('name')->
            groupBy('name')->
            get();

        // dd($products);

        $this->tableHeaderLength += count($products) * 2;

        // dd($data);
        return view('excel_export.cross_night_shift_excel', [
            'receiptList' => $data_collection2,
            'nshift' => [$nshift],
            'product_name' => $products,
            'start_date' => $this->start,
            'stop_date' => $this->stop,
            // 'title' => $this->title,
            // 'nshift_count' => $this->shift_count,
        ]);
    }

    public function title(): string
    {
        return "Cross";
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
                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(30);
            },
        ];
    }

    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && strlen($value) > 15 || strlen(trim($value, ' ')) > 10) {

            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }
        // else return default behavior
        return parent::bindValue($cell, $value);
    }
}
