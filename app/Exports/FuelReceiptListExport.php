<?php

namespace App\Exports;

use Illuminate\Support\Facades\DB;
use Maatwebsite\Excel\Concerns\WithMultipleSheets;
use Maatwebsite\Excel\Concerns\WithEvents;
use Maatwebsite\Excel\Events\AfterSheet;
use Maatwebsite\Excel\Concerns\WithCalculatedFormulas;
use Maatwebsite\Excel\Concerns\RegistersEventListeners;
use Maatwebsite\Excel\Concerns\WithMapping;

use Maatwebsite\Excel\Events\BeforeExport;
use Log;
use PDF;


class FuelReceiptListExport implements WithMultipleSheets, WithEvents, WithCalculatedFormulas,WithMapping
{
    use  RegistersEventListeners;
    private $request;
    protected $nshift_size = 0;
    protected $len = 6;

    public function __construct($request)
    {
        $this->request = $request;
    }

    public function sheets(): array
    {
        Log::debug("PF ***** sheets() start *****");

        if (
            !isset($this->request->fuel_start_date) &&
            !isset($this->request->fuel_stop_date)
        ) {
            $start_ = date("Y-m-d", time());
            $stop_ = date("Y-m-d", time());
        } else {
            $start_ = date('Y-m-d', strtotime($this->request->fuel_start_date));
            $stop_ = date('Y-m-d', strtotime($this->request->fuel_end_date));
        }
        $nshift = DB::table('nshift')
            ->select('nshift.*');

        $nshift = $nshift->whereBetween(
                'nshift.created_at',
                [$start_ . ' 00:00:00', $stop_ . ' 23:59:59']
            );

        $nshift = $nshift->get()->map(function ($d) {
            $d->in = (!isset($d->in)) ? '' : date('dMy H:i:s', strtotime($d->in));
            $d->out = (!isset($d->out)) ? '' : date('dMy H:i:s', strtotime($d->out));
            return $d;
        });

        $this->nshift_size =  sizeof($nshift);
        $nsheets = [];
        $i = 1;

        foreach ($nshift as $n) {
            if (!empty($n)) {
                Log::debug("PF nshift loop : nshift-->" . json_encode($n));
            }

            $user = DB::table('users')->where('systemid', $n->staff_systemid)->first();

            $staff_id = $user->id;
            $start = /* $n->in */ $start_;
            $stop = /* empty($n->out) ? $this->request->fuel_end_date : $n->out; */ $stop_;
            $title = "" . $i;

            Log::debug("PF nshift loop : BEFORE nsheets");

            $nsheets[] = new ShiftFuelReceiptExportTemplate(
                $staff_id,
                $start,
                $stop,
                $title,
                $n->id,
                $i
            );
            $i++;


            Log::debug("PF nshift loop : AFTER nsheets=" . count($nsheets));
        }


        Log::debug('PF BEFORE MainFuelReceiptListExport');

        $sheets = [
            new MainFuelReceiptListExport($this->request, 'Main'),
            // new CrossNightExport($start_, $stop_),
            ...$nsheets,
        ];

        Log::debug('PF AFTER MainFuelReceiptListExport');

        Log::debug("PF ***** sheets() end *****");

        return $sheets;
    }

    public function registerEvents(): array
    {
        $numOfRows = $this->len;
        $start = $this->nshift_size + 6;
        $totalRow = $numOfRows;
        return [
            BeforeExport::class => function (BeforeExport $event) {
                $event->writer->getProperties()->setCreator('You')->setTitle("Main");
            },

            AfterSheet::class => function (AfterSheet $event) use($numOfRows,$start,$totalRow) {
                // 1
            $event->sheet->setCellValue('A1', 'No');
            },
        ];

    }

    public function map($data): array
    {
        return [

        ];
    }
}
