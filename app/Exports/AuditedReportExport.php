<?php

namespace App\Exports;


use Illuminate\Contracts\View\View;
use Maatwebsite\Excel\Concerns\FromView;
use PhpOffice\PhpSpreadsheet\Cell\Cell;
use PhpOffice\PhpSpreadsheet\Cell\DataType as PhpSpreadsheetDataType;
use PhpOffice\PhpSpreadsheet\Cell\DefaultValueBinder;
use PhpOffice\PhpSpreadsheet\Style\NumberFormat;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\ShouldAutoSize;
use Maatwebsite\Excel\Concerns\WithColumnFormatting;
use Maatwebsite\Excel\Concerns\WithCustomValueBinder;
use DB;


class AuditedReportExport
    extends DefaultValueBinder
implements FromView, WithEvents, ShouldAutoSize, WithCustomValueBinder, WithColumnFormatting
{
    public $request;

    public function __construct($request)
    {
        $this->request = $request;
    }
    /**
     * @return \Illuminate\Support\View
     */
    public function view(): View
    {
        $query = "
            SELECT
                ar.*,
                p.name as name,
                p.thumbnail_1 as thumbnail_1,
                p.systemid as psystemid,
                IFNULL(pb.barcode,p.systemid) as barcode
            FROM
                auditedreport ar
            LEFT JOIN product p ON p.id = ar.product_id
            LEFT JOIN productbarcode pb
                ON pb.product_id = p.id AND
                pb.selected = 1 AND
                pb.deleted_at is NULL
            WHERE
                ar.systemid = '".$this->request->doc_no."'
            ;
        ";
        $data = DB::select(DB::raw($query));
        return view('excel_export.audited_report_excel', [
            'auditedReport' => $data,
            'docId' => $this->request->doc_no
        ]);
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
}
