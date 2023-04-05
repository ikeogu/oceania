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
			background:#ff7e30;
			border-color:#ff7e30
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
            padding-top: 4px !important;
            padding-bottom: 4px !important;
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
        .stmt_footer {
          position: fixed;
          left: 0;
          bottom: 0;
          width: 100%;
          text-align: right;
        }
    </style>
</head>
{{--{{ $requestValue['button_filter'] }}--}}

<body>
	<table border="0" style="width:100%; border-collapse: collapse"
		cellspacing="0" cellpadding="0">
		<tr>
			<td valign="center" rowspan="2" colspan="2">
    			<b style="font-size:30px;font-weight:700;word-wrap:normal;">
    			Credit Account Statement
    			</b>
			</td>
		</tr>
	</table>

    <table border="0" cellpadding="0" cellspacing="0"
		class="table" id="item" style="margin-top: 5px; width:100%">
		<tr class="table-td pb-0 mb-0">
			<td class="text-left" style="vertical-align:middle !important;border-style: none;font-size:15px!important">
				Account Name
			</td>
			<td class="text-right pr-0 mr-0" style="border-style: none">
				<p class="pr-0 mr-0 mb-0 float-right"
					style="font-size:24px;font-weight:700;word-wrap:normal;">
					 {{$company->account_name}}
				</p>
			</td>
		</tr>
		<tr class="table-td pt-0 mt-0">
			<td class="text-left" style="border-style: none;font-size:15px!important">
				Account No
			</td>
			<td style="text-align:right;border-style: none;font-size:15px!important">
					{{ $company->systemid }}
			</td>
		</tr>
        <tr class="table-td pt-0 mt-0">
			<td class="text-left" style="border-style: none;font-size:15px!important">
				Credit Limit
			</td>
			<td style="text-align:right;border-style: none;font-size:15px!important">
					{{ number_format($company->credit_limit / 100,2) }}
			</td>
		</tr>
		<tr class="table-td pt-0 mt-0">
			<td class="text-left" style="border-style: none;font-size:15px!important!important">
				Credit Limit Balance
			</td>
			<td style="text-align:right;border-style: none;font-size:15px!important">
					<!--
                    {{ number_format($ftotal,2) }}
					-->
					<!-- Squidster: TODO: Remaining Credit Limit -->
					{{-- {{ number_format(($company->credit_limit/100) - $ftotal,2) }} --}}
                    {{ number_format((($company->credit_limit - $current_ledger_total->total)/100),2 ) }}

			</td>
		</tr>
		<tr class="table-td pt-0 mt-0">
			<td class="text-left" style="border-style: none;font-size:15px !important">
			</td>
			<td style="text-align:right;border-style: none;font-size:15px !important">
					{{$company->stmt_start_date}} - {{$company->stmt_end_date}}
			</td>
		</tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="0" class="table"
		id="item" style="margin-top: 0px width:100%">
        <thead class="thead-dark">
        <tr id="table-th" style="border-style: none">
            <th valign="middle" class="text-center" style="width:5%;">No</th>
            <th valign="middle" class="text-left" style="padding-left:5px">Document No.</th>
            <th valign="middle" class="text-center " style="width:20%;"> Last Update</th>
            <th valign="middle" class="text-center" style="width:10%;">Amount</th>
        </tr>
		</thead>
        @foreach($transactions as $key => $tx)
            <tr class="table-td">
                <td class="text-center" style="border-style: none">
					{{$key+1}}
				</td>
                <td class="text-left" style="border-style: none; padding-right:5px">
    				{{$tx['sysid']}}
				</td>
                <td class="text-center" style="border-style: none">
                    {{$tx['date']}}
                 </td>
                <td style="text-align:right;border-style: none;padding-right:5px">
                    {{$tx['amount'] }}
                </td>
            </tr>
        @endforeach
        <tr>
            <td colspan="4" valign="middle">
                <div  style="border-top: 1px solid #a0a0a0;"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2">
				<p style="font-size:12px;margin-top:-10px">
					{{ date('dMy H:i:s') }}
				</p>
			</td>
            <td colspan="2" valign="middle" class="text-right"
				style="margin-right:0!important; ">
                <p style="padding-right:0;text-decoration-line: none;font-size: 15px;font-weight: 700;padding: 0;margin: 0;margin-top: -5px">
                    Total MYR {{ number_format($ftotal,2) }}
                </p>
            </td>
        </tr>
    </table>
	<!--
    <div class="stmt-footer">
      <p>
	  {{ date('dMy H:i:s') }}
      </p>
    </div>
	-->
</body>
</html>
