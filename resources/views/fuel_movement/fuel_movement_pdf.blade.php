<!DOCTYPE html PUBLIC "-//W3C//DTD HTML 4.01 Transitional//EN" "http://www.w3.org/TR/html4/loose.dtd">
<html>
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />

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
         .table td {
            border-bottom: 1px solid #dee2e6;
            border-right: 1px solid #dee2e6;
}
        .sty_data{
            width:25% ;
			background-color: transparent;
			font-size: 1.3rem;
			text-align: left;
        }
		.pend {
			text-align:right;
			font-weight:regular;
			font-size: 1.3rem;
		}
		.ptitle {
			width:40%;
			font-size:33px;
			font-weight:700;
			margin-bottom:0;
			padding-bottom:0;
			padding-left:0;
			margin-left:0;
			text-align:left;
		}
    </style>
</head>
<body>
    <table class="table table-bordered">
        <thead>
        <tr>
            <th valign="bottom"
				class="ptitle">
				Fuel Movement</th>
            <th class="sty_data">{{$superdata['fuel_prod_name']}} <br>
			<span style="font-weight:regular;font-size:1.1rem">
			{{$superdata['systemid']==null?'':$superdata['systemid']->systemid }}
			</span>
			</th>

            <th></th>

            <th class="pend">
				{{$superdata['location'] }}
				<br>
				<span style="font-size:1.1rem" class="pend">
				{{$superdata['date'] }}
				</span>
			</th>
        </tr>
    </thead>
    </table>

    <table id="fuelMgt" class="table table-bordered">
        <thead class="thead-dark">
        <tr class="">
            <th style="height:30px; width:10px">&nbsp;No&nbsp;</th>
            <th style="width:140px">Date</th>
            <th style="width:100px">C/Forward</th>
            <th style="width:100px">Sales</th>
            <th style="width:100px">Receipt</th>
            <th style="width:100px">Book</th>
            <th style="width:100px">Tank Dip</th>
            <th style="width:100px">&nbsp;Daily&nbsp;Variance&nbsp;</th>
            <th style="width:150px">Cumulative</th>
            <th style="width:50px">%</th>
        </tr>
        </thead>

        <tbody>
            @foreach ($superdata['data'] as $key => $row)
                <tr>
                    <td style="text-align: center;">{{$key+1}}</td>
                    <td style="text-align: center;">{{Date('dMy',(strtotime($row->date)))}}</td>
                    <td style="text-align: center;">{{$row->cforward}}</td>
                    <td style="text-align: center;">{{$row->sales}}</td>
                    <td style="text-align: center;">{{$row->receipt}}</td>
                    <td style="text-align: center;">{{$row->book}}</td>
                    <td style="text-align: center;">{{$row->tank_dip}}</td>
                    <td style="text-align: center;">{{$row->daily_variance}}</td>
                    <td style="text-align: center;">{{$row->cumulative}}</td>
                    <td style="text-align: center;">{{$row->percentage}}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
	<div>
		<span>
		*Column Sales, Receipt, Book and Tank Dip are in litre
		</span>
		<br>
		<span>
		{{ date('dMy H:i:s') }}
		</span>
	</div>

</body>
</html>
