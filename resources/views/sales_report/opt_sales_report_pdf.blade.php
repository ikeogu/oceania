<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'OPOSsum') }}</title>

    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            color: #212529;
            text-align: left;
            background-color: #fff;
        }
		.bg-refund{
			color:#fff;
			background:#0f0e0e;
			border-color:#887c74
		}
        .table{
            width: 100%!important;
            border-style: none;
        }
        .thead-dark {
            color: white;
            border-color: #343a40;
            background-color: #343a40;

        }
        #table-th th{
            font-size: 12px!important;
        }
        .text-center{
            text-align: center;
        }
        .text-left{
            text-align: left;
        }
        .text-right{
            text-align: right;
        }
        .table-td td{
            font-size: 12px!important;
        }
        p {
            margin-top: 0;
            margin-bottom: 0rem;
            font-size: 12px;
        }

        hr{
            margin-bottom: 0;
        }
        #item tr  td {
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            vertical-align: middle !important;
        }
        #item tr th{
            font-size: 12px;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
            vertical-align: middle !important;
        }

        td{
            border-style: none;
        }
        th{
            border-style: none;
        }

        .text-bold {
            font-weight: bold;
            font-size: 12px;
        }

        span {
            font-size: 12px;
        }

		tr td span{
            /*width: 80px !important;*/
            /*height: 60px !important;*/
			text-align: center;
			vertical-align: middle;
			font-size: 12px;
			cursor: pointer;
			padding: 10px 20px;
			color: black;
			display: inline-block;
			font-weight: 400;
			margin-top: 10px;
        }
        .active{
            border-radius: 10px;
            color: white;
            padding: 10px 25px;
            background-color: black;
        }
        .rad-info-box .heading {
            font-size: 1.2em;
            font-weight: 300;
            text-transform: uppercase;
        }
    </style>
</head>
{{--{{ $requestValue['button_filter'] }}--}}

<body>
	<table border="0" style="width:100%; border-collapse: collapse"
		cellspacing="0" cellpadding="0">

		<tr>
			<td valign="center" rowspan="2" colspan="2">
			<b style="font-size:28px;font-weight:700;word-wrap:normal;">
			Outdoor Payment Terminal Sales Report
			</b>
			</td>
			<td valign="bottom" colspan="3" align="right"
				style="font-size: 15px">
				{{$location->name}}<br>{{$location->systemid}}
			</td>
		</tr>
		<tr>
			<td valign="bottom" colspan="5" align="right" >
			<p style="font-size: 12px">
			@if(!empty($requestValue['opt_start_date']) &&
				!empty($requestValue['opt_end_date']))
				{{ date('dMy',strtotime($requestValue['opt_start_date'])) }} -
				{{ date('dMy',strtotime($requestValue['opt_end_date'])) }}
			@endif
			</p>
		  </td>
		</tr>
	</table>

    <table border="0" cellpadding="0" cellspacing="0" class="table"
		id="item" style="margin-top: 0px;width:100%">
		<tr class="pr-0 mr-0">
			<td colspan="5" valign="middle">
				<div  style="border-top: 2px solid black;"></div>
			</td>
		</tr>
		@if($visa_amount > 0)
		<tr class="p-0 m-0" style="">
			<td class="p-0 m-0 text-left" style="">
				Total Visa
			</td>
			<td class="p-0 m-0 text-right" style="">
				{{number_format($visa_amount,2) }}
			</td>
		</tr>
		@endif
		@if($master_amount > 0)
		<tr class="pr-0 mr-0">
			<td class="text-left" style="border-style: none">				
				Total Master
			</td>
			<td style="text-align:right">			
				{{number_format($master_amount,2) }}
			</td>
		</tr>
		@endif
		@if($amex_amount > 0)
		<tr class="pr-0 mr-0">
			<td class="text-left" style="border-style: none">				
				Total Amex
			</td>
			<td style="text-align:right;border-style: none">				
				{{number_format($amex_amount,2) }}
			</td>
		</tr>
		@endif
		@if($mydebit_amount > 0)
		<tr class="pr-0 mr-0">
			<td class="text-left" style="border-style: none">				
				Total MyDebit
			</td>
			<td style="text-align:right;border-style: none">				
				{{number_format($mydebit_amount,2) }}
			</td>
		</tr>
		@endif
		<tr class="pr-0 mr-0">
			<td colspan="5" valign="middle">
				<div  style="border-top: 1px solid #a0a0a0;"></div>
			</td>
		</tr>
	</table>   
		
		<?php 
		$start = date('Y-m-d',strtotime($_REQUEST['opt_start_date']));
		$stop = date('Y-m-d',strtotime($_REQUEST['opt_end_date']));
		
		Log::Debug('Start = '. $start);
		
		for ($pump =1;$pump<=20;$pump++) {

			$pump_visa_amount = 0;
			$pump_master_amount = 0;
			$pump_amex_amount = 0;
			$pump_mydebit_amount = 0;
			$pump_desc = "";

			$sales_data = DB::table('invssi_sale')->		
						join('local_pumpopt', 'invssi_sale.terminalId', '=', 'local_pumpopt.opt_terminal_id')->
						whereBetween('invssi_sale.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])->
						where('local_pumpopt.pump_id', $pump)->
						orderBy('local_pumpopt.pump_id')->                          
						get();	

			Log::Debug('Count = '. DB::table('invssi_sale')->				
						join('local_pumpopt', 'invssi_sale.terminalId', '=', 'local_pumpopt.opt_terminal_id')->
						whereBetween('invssi_sale.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])->
						where('local_pumpopt.pump_id', $pump)->
						orderBy('local_pumpopt.pump_id')->count());		
			
			Log::Debug('Pump = '. $pump);				

			$count = DB::table('invssi_sale')->				
					join('local_pumpopt', 'invssi_sale.terminalId', '=', 'local_pumpopt.opt_terminal_id')->
					whereBetween('invssi_sale.created_at', [$start . ' 00:00:00', $stop . ' 23:59:59'])->
					where('local_pumpopt.pump_id', $pump)->
					orderBy('local_pumpopt.pump_id')->count();

           // if($count > 0){
			foreach ($sales_data as $sale) {
				// Generate PDF filename
						
				foreach(preg_split("/((\r?\n)|(\r\n?))/", $sale->receipt) as $line){
						
					if(strpos($line, "Card Desc") !== false) {
						[$x, $desc] = preg_split("/Desc/", $line);
						$desc = trim($desc);                                     
						Log::info('desc  =' . $desc); 
						break;                      
							
						}
						
					  Log::info('Line  =' . $line); 
					}                            
					Log::info('Card = ' . $desc);
							
					if($desc == "Visa"){
						$pump_visa_amount = $pump_visa_amount + $sale->amount; 
						Log::debug('Pump Visa amount=' . $pump_visa_amount);
										
					}   
					elseif($desc == "Mastercard"){
						$pump_master_amount = $pump_master_amount + $sale->amount;  
						Log::debug('Pump Master amount=' . $pump_master_amount);
											
						}
					elseif($desc == "Amex"){
						$pump_amex_amount = $pump_amex_amount + $sale->amount;
						Log::info('Pump Amex amount=' . $pump_amex_amount); 
											
						}   
					elseif($desc == "Mydebit"){
						$pump_mydebit_amount = $pump_mydebit_amount + $sale->amount; 
						Log::info('Pump Mydebit amount=' . $pump_mydebit_amount); 
											
					}   
				}  
			                           
							
			?>
				<h3 style="margin-bottom:5px!important;margin-top:0px !important" class=""> 
					Pump {{ $pump }}
				</h3>
				<table border="0" cellpadding="0" cellspacing="0" class="table"
					id="item" style="margin-top: 0px width:100%">
					
					@if($pump_visa_amount > 0)
					<tr class="" style="margin:0 !important;padding:0 !important;height:0.7rem">
						<td class="m-0 p-0 text-left" style="border-style: none">					
						Visa
						</td>
						<td class="m-0 p-0" style="text-align:right;border-style: none">					
							{{number_format($pump_visa_amount,2) }}
						</td>
					</tr>
					@endif
					@if($pump_master_amount > 0)
					<tr class="m-0 p-0" style="height:0.7rem">
						<td class="text-left" style="border-style: none">					
							Master
						</td>
						<td style="text-align:right;border-style: none">					
							{{number_format($pump_master_amount,2) }}
						</td>

					</tr>
					@endif
					@if($pump_amex_amount > 0)
					<tr class="m-0 p-0" style="height:0.7rem">
						<td class="text-left" style="border-style: none">					
						Amex
						</td>
						<td style="text-align:right;border-style: none">			
							{{number_format($pump_amex_amount,2) }}
						</td>
					</tr>
					@endif
					@if($pump_mydebit_amount > 0)
					<tr class="m-0 p-0" style="height:0.7rem">
						<td class="text-left" style="border-style: none">					
							MyDebit
						</td>
						<td style="text-align:right;border-style: none">					
							{{number_format($pump_mydebit_amount,2) }}
						</td>
					</tr>
					@endif
					<tr class="m-0 p-0" style="height:0.7rem">
						<td colspan="5" valign="middle">
							<div  style="border-top: 1px solid #a0a0a0;"></div>
						</td>
					</tr>
				</table>

				<table border="0" cellpadding="0" cellspacing="0" class="table"
					id="item" style="margin-top: 0px width:100%">
					<thead class="bg-refund">
					<tr id="table-th" style="border-style: none">
						<th valign="middle" class="text-center" style="width:10%;">No</th>
						<th valign="middle" class="text-center" style="width:20%;">Type</th>
						<th valign="middle" class="text-right" style="width:30%">Card No</th>
						<th valign="middle" class="text-right" style="width:20%;">Amount</th>
						<th valign="middle" class="text-center" style="width:20%;">Status</th>
					</tr>
					</thead>
					<?php $i =1; ?>								
					@foreach ($sales_data as $sale) 				
						<tr class="table-td">
							<td class="text-center" style="border-style: none">                       
								{{$i }}
							</td>
							<td class="text-center" style="border-style: none">
							@foreach(preg_split("/((\r?\n)|(\r\n?))/", $sale->receipt) as $line)	
								@if (strpos($line, "Card Desc") !== false) 
										<?php  [$x, $desc] = preg_split("/Desc/", $line); ?>                            
											{{ trim($desc) }}
								@endif	                  
							@endforeach 
							</td>
							<td class="text-right" style="border-style: none">
							@foreach(preg_split("/((\r?\n)|(\r\n?))/", $sale->receipt) as $line)	
								@if (strpos($line, 'Card') !== false && strpos($line, 'Card Desc') === false && strpos($line, 'Card Exp') === false) 					
									<?php	[$x, $cardno] = preg_split("/Card/", $line); ?>
									{{ $cardno }}
								@endif                       
							@endforeach
							</td>
							<td style="text-align:right;border-style: none">                
								{{number_format($sale->amount,2) }}
							</td>
							<td style="text-align:center;border-style: none" colspan="5">
							@foreach(preg_split("/((\r?\n)|(\r\n?))/", $sale->receipt) as $line)						 
								@if (strpos($line, 'APPROVED') !== false) 					
							  	APPROVED 00								
								@endif	  
							@endforeach	                      
							</td>
						</tr>
						<?php $i=$i+1; ?>
					@endforeach	

					<tr>
						<td colspan="5" valign="middle">
							<div  style="border-top: 1px solid #a0a0a0;"></div>
						</td>
					</tr>

				</table>
   
			<?php // }
		}
			?>

	<table>
		<tr>
			<td colspan="3" align="left" valign="middle">
			<p style="text-decoration-line: none;font-size: 15px;font-weight: 300;padding: 0;margin: 0;margin-top: -5px; ">
				 {{date('dMy h:i:s')}}
			</p>
			</td>
			<td colspan="8" align="right" valign="middle">
			   <p style="text-decoration-line: none;font-size: 15px;font-weight: 300;padding: 0;margin: 0;margin-top:-5px;" ></p>
			</td>

		</tr>
	</table>
</body>
</html>
