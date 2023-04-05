<?php

namespace App\Exports;



use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType;
use Illuminate\Support\Facades\DB;

class FuelFullTankReceiptExport implements FromView, WithHeadings, WithEvents, WithCustomValueBinder, ShouldAutoSize
{
    protected static $numericColumns = ['C'];
    protected $request;
    protected $tableHeaderLength = 11;

    function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\Collection
     */
    public function view(): View
    {

        if (is_null($this->request)) {
            $start = date("Y-m-01");
            $stop = date("Y-m-t");
        } else {
            $start = date('Y-m-d', strtotime($this->request->fulltank_start_date));
            $stop = date('Y-m-d', strtotime($this->request->fulltank_end_date));
        }

        $data = DB::table('fuelfulltank_receipt')
            ->leftJoin('fuelfulltank_receiptlist', 'fuelfulltank_receiptlist.fuel_fulltank_receipt_id', '=', 'fuelfulltank_receipt.id')
            ->leftJoin('fuelfulltank_receiptproduct', 'fuelfulltank_receiptproduct.fulltank_receipt_id', '=', 'fuelfulltank_receipt.id')
            ->leftJoin('fuelfulltank_receiptdetails', 'fuelfulltank_receiptdetails.fulltank_receipt_id', '=', 'fuelfulltank_receipt.id')
            ->whereBetween('fuelfulltank_receiptlist.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])
            ->select(
                'fuelfulltank_receipt.*',
                'fuelfulltank_receiptlist.created_at as Fcreated_at',
                'fuelfulltank_receiptlist.total as Total',
                'fuelfulltank_receiptlist.pump_no as pump_no',
                'fuelfulltank_receiptproduct.quantity as quantity',
                'fuelfulltank_receiptproduct.price as price',
                'fuelfulltank_receiptproduct.name as product_name',
                'fuelfulltank_receiptdetails.item_amount as item_amount',
                'fuelfulltank_receiptdetails.*'

            )
            ->get();
        $products = DB::table('fuelfulltank_receiptproduct')
            ->select('name')
            ->groupBy('name')
            ->get();
        // $products_qty = DB::table('fuelfulltank_receiptproduct')
        //     ->select('name', 'quantity')
        //     ->groupBy('name', 'quantity')
        //     ->get();



        $this->tableHeaderLength += count($products) * 2;
        return view('excel_export.fuel_fulltank_receiptlist_excel', [
            'receiptList' => $data,
            'product_name' => $products,
            'start_date' => $start,
            'stop_date' => $stop
        ]);
    }
    /**
     * Write code on Method
     *
     * @return response()
     */
    public function columnFormats(): array
    {
        return [
            'C' => DataType::TYPE_STRING,
        ];
    }
    public function headings(): array
    {
        return [

            'No',
            'Date',
            'Receipt ID',
            'Total',

            '95',
            '97',
            'B7',
            'B20',
            'Cash',
            'Credit Card',
            'Wallet',
            'Credit Account',

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
            AfterSheet::class    => function (AfterSheet $event) {

                $event->sheet->getDelegate()->getRowDimension('1')->setRowHeight(20);
                $event->sheet->getDelegate()->getStyle(static::columnLetter($this->tableHeaderLength))
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB(
                        '000000',
                    );
                $event->sheet->getStyle(static::columnLetter($this->tableHeaderLength))->getFont()->setBold(true)->getColor()->setRGB('ffffff');
            },
        ];
    }
    public function bindValue(Cell $cell, $value)
    {
        $cell->setValueExplicit($value, \PhpOffice\PhpSpreadsheet\Cell\DataType::TYPE_STRING);

        return true;
    }
    public static function columnLetter($c)
    {

        $c = intval($c);
        if ($c <= 0) return '';

        $letter = '';

        while ($c != 0) {
            $p = ($c - 1) % 26;
            $c = intval(($c - $p) / 26);
            $letter = chr(65 + $p) . $letter;
        }

        return 'A3:' . $letter . '3';
    }
}
