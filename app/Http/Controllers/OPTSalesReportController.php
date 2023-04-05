<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\FuelReceipt;
use App\Models\FuelReceiptdetails;
use App\Models\FuelReceiptList;
use App\Models\Location;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use \App\Classes\SystemID;
use Illuminate\Support\Facades\Storage;
use PDF;

class OPTSalesReportController extends Controller
{
    //
    public static function generate_opt_sales_pdf(Request $request)
    {
        
        //$dimension= array(0,0,226.77,500);
        try {
            Log::debug('Request: ' . json_encode($request->all()));
        $company = Company::first();

        $currency = $company->currency->code ?? 'MYR';
        $location = DB::table('location')->first();

        //Change date Format
        $requestValue = $request->all();
        $start = date('Y-m-d', strtotime($request->opt_start_date));
        $stop = date('Y-m-d', strtotime($request->opt_end_date));

        Log::debug('Start Date: ' . $start);
        Log::debug('Stop Date: ' . $stop);

        $sales = DB::table('invssi_sale')->
            select('local_pumpopt.pump_id', 'invssi_sale.*')->
            join('local_pumpopt', 'invssi_sale.terminalId', '=', 'local_pumpopt.opt_terminal_id')->
            whereBetween('invssi_sale.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])->
            get();
        
        Log::debug("Count = " . DB::table('invssi_sale')->				
        join('local_pumpopt', 'invssi_sale.terminalId', '=', 'local_pumpopt.opt_terminal_id')->
        whereBetween('invssi_sale.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])->
        orderBy('local_pumpopt.pump_id')->count());
         
           // 
           $visa_amount = 0;
           $master_amount = 0;
           $amex_amount = 0;
           $mydebit_amount = 0;
           $desc = "";
            foreach ($sales as $sale) {
                // Generate PDF filename
                          
                    foreach(preg_split("/((\r?\n)|(\r\n?))/", $sale->receipt) as $line){
                        
                                if(strpos($line, "Card Desc") !== false) {
                                    [$x, $desc] = preg_split("/Desc/", $line);
                                 //   $str = explode(" ", $line);
                                 //   $len = strlen($str);
                                    $desc = trim($desc);                                     
                                    Log::info('desc  =' . $desc); 
                                    break;                            
                                    
                                }                         
                              //  Log::info('Line  =' . $line); 
                            }                            
                                    Log::info('Card = ' . $desc);
                              
                                    if($desc == "Visa"){
                                        $visa_amount = $visa_amount + $sale->amount; 
                                            Log::debug('Total Visa amount=' . $visa_amount);
                                        
                                    }   
                                    elseif($desc == "Mastercard"){
                                        $master_amount = $master_amount + $sale->amount;  
                                            Log::debug('Total Master amount=' . $master_amount);
                                            
                                    }
                                    elseif($desc == "Amex"){
                                        $amex_amount = $amex_amount + $sale->amount;
                                            Log::info('Total Amex amount=' . $amex_amount); 
                                            
                                    }   
                                    elseif($desc == "Mydebit"){
                                        $mydebit_amount = $mydebit_amount + $sale->amount; 
                                            Log::info('Total Mydebit amount=' . $mydebit_amount); 
                                            
                                    }    
                             
                 }    

            $pump_data = DB::table('local_pumpopt')->
                 select('local_pumpopt.pump_id')->	
                 orderBy('local_pumpopt.pump_id')-> 
                 distinct('local_pumpopt.pump_id')->                         
                 get();	

            $pdf = PDF::setOptions([
                'isHtml5ParserEnabled' => true,
                'isRemoteEnabled' => true
            ])->loadView(
                'sales_report.opt_sales_report_pdf',
                compact(
                'sales',
                'visa_amount', 
                'master_amount', 
                'amex_amount', 
                'mydebit_amount', 
                'location', 
                'currency', 
                'company', 
                'pump_data',                 
                )
            );

            $pdf->getDomPDF()->setBasePath(public_path() . '/');
            $pdf->getDomPDF()->setHttpContext(
                stream_context_create([
                    'ssl' => [
                        'allow_self_signed' => true,
                        'verify_peer' => false,
                        'verify_peer_name' => false,
                    ],
                ])
            );
            $pdf->setPaper('A4', 'portrait');
            return $pdf->download('OutdoorPaymentTerminalReport.pdf');          

        } catch (\Exception $e) {
            return [
                "message" => $e->getMessage(),
                "error" => false,
            ];
        }
    }

    

}
