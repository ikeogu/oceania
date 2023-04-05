<?php

namespace App\Exports;

use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithHeadings;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Cell\DataType;

use stdClass;

class CstoreExport  extends DefaultValueBinder implements FromView, WithEvents, WithCustomValueBinder, ShouldAutoSize
{
    protected $request;
    protected $tableHeaderLength = 12;

    function __construct($request)
    {
        $this->request = $request;
    }

    public function view(): View
    {
        if (is_null($this->request)) {
            $start = date("Y-m-01");
            $stop = date("Y-m-t");
        } else {
            $start = date('Y-m-d', strtotime($this->request->cstore_start_date));
            $stop = date('Y-m-d', strtotime($this->request->cstore_end_date));
        }

        $product = "
            SELECT
                cr.id,
                u.systemid as staff_id,
                u.fullname as staff_name,
                cr.systemid as receipt_i,
                cr.status,
                rpd.name as product_name,
                rpd.quantity,
                rpd.price,
                id.rounding,
                id.amount,
                rd.item_amount,
                rd.total,
                rd.wallet,
                rd.change,
                rd.creditcard,
                rd.void,
                rd.cash_received,
                rd.created_at as date,
                nullif(rf.refund_amount,0) as refund
            FROM

                cstore_receiptproduct rpd,
                cstore_itemdetails id,
                cstore_receiptdetails rd,
                users u,
                cstore_receipt cr
            LEFT JOIN cstore_receiptrefund rf ON rf.cstore_receipt_id = cr.id

            WHERE
                cr.id =rpd.receipt_id AND
                rpd.id = id.receiptproduct_id AND
                cr.id = rd.receipt_id AND
                cr.staff_user_id = u.id  AND
                cr.created_at BETWEEN '".$start." 00:00:00' AND '".$stop." 23:59:59'
            ORDER BY
                receipt_i ASC
            ;
        ";

        $query = "
            SELECT
                p.name
            FROM
                prd_inventory pi
            LEFT JOIN  product p ON p.id = pi.product_id
            GROUP BY
                p.name
            UNION
            SELECT
                p.name
            FROM
                prd_openitem po
            LEFT JOIN  product p ON p.id = po.product_id
            GROUP BY
                p.name
        ;
        ";
        $all_products = collect(DB::select(DB::raw($query)));

        $data = $this->collection_transformer(collect(DB::select(DB::raw($product))));

        $this->tableHeaderLength += count($all_products);
        return view('excel_export.cstore_receiptlist_excel', [
            'cstore_products' => $data,
            'product_name' => $all_products,
            'start_date' => $start,
            'stop_date' => $stop
        ]);
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

                $event->sheet->getDelegate()->
                    getRowDimension('1')->
                    setRowHeight(18);

                $event->sheet->getDelegate()->
                    getStyle(static::columnLetter($this->tableHeaderLength))
                    ->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()
                    ->setARGB('000000');

                $event->sheet->getStyle(static::columnLetter(
                    $this->tableHeaderLength))->
                    getFont()->
                    setBold(true)->
                    getColor()->
                    setRGB('ffffff');
            },

        ];
    }
    public function bindValue(Cell $cell, $value)
    {
        if (is_numeric($value) && strlen($value) > 10) {

            $cell->setValueExplicit($value, DataType::TYPE_STRING);
            return true;
        }

        // else return default behavior
        return parent::bindValue($cell, $value);
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

    public function collection_transformer($collect)
    {
        $new_arr = collect();
        $collect = $collect->each(function($item) use ($new_arr) {
            if ($new_arr->where('receipt_i', $item->receipt_i)->count() > 0) {

                $new_arr->where('receipt_i', $item->receipt_i)->
                first()->product_name[] = $item->product_name;

                $new_arr->where('receipt_i', $item->receipt_i)->
                first()->price[] =  $item->amount;

            } else {
                $item->product_name = [$item->product_name];
                $item->price = [$item->amount];
                $new_arr->push($item);

            }

        });

        return $new_arr;
    }
}
