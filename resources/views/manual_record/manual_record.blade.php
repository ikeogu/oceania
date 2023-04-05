@extends('common.web')
@section('styles')

<script type="text/javascript" src="{{ asset('js/console_logging.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/qz-tray.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/opossum_qz.js') }}"></script>

<style>
table.dataTable thead .sorting,
table.dataTable thead .sorting_asc,
table.dataTable thead .sorting_desc {
    background : none;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: black !important;
    font-weight: normal !important;
}

#receipt-table_length,
#receipt-table_filter,
#receipt-table_info,
.paginate_button {
    color: white !important;
}

#eodSummaryListModal-table_paginate,
#eodSummaryListModal-table_previous,
#eodSummaryListModal-table_next,
#eodSummaryListModal-table_length,
#eodSummaryListModal-table_filter,
#eodSummaryListModal-table_info {
    color: white !important;
}

.paging_full_numbers a.paginate_button {
    color: #fff !important;
}

.paging_full_numbers a.paginate_active {
    color: #fff !important;
}

table.dataTable th.dt-right,
table.dataTable td.dt-right {
    text-align: right !important;
}

td {
    vertical-align: middle !important;
    text-align: center;
}

.bg-fuel-refund {
    color: white !important;
    border-color: #ff7e30 !important;
    background-color: #ff7e30 !important;
}
.boxhead a:hover {
    text-decoration: none;
}

.bg-total{
    background-color: rgb(255,126,48) !important;

}
.modal-inside .row {
    padding: 0px;
    margin: 0px;
    color: #000;
}

.modal-body {
    position: relative;
    flex: 1 1 auto;
    padding: 0px !important;
}

table.dataTable.display tbody tr.odd>.sorting_1 {
    background-color: unset !important;
}

tr:hover,
tr:hover>.sorting_1 {
    background: none !important;
}

table.dataTable.display tbody tr.odd>.sorting_1,
table.dataTable.order-column.stripe tbody tr.odd>.sorting_1 {
    background: none !important;
}
table, th, td {
  border: 0.3px solid rgb(243, 237, 237) !important;
  border-collapse: collapse !important;
  padding: 0.2% !important;
}
button:focus{
    outline: 0;
}
button:hover{
     background: #1c9939 !important;
     color: #fff;
}
table.dataTable.order-column tbody tr>.sorting_1,
table.dataTable.order-column tbody tr>.sorting_2,
table.dataTable.order-column tbody tr>.sorting_3,
table.dataTable.display tbody tr>.sorting_1,
table.dataTable.display tbody tr>.sorting_2,
table.dataTable.display tbody tr>.sorting_3 {
    background-color: #fff !important;
}
label {
            float: left;
        }

        span {
            display: block;
            overflow: hidden;
            padding: 0px 4px 0px 6px;
        }

        input {
            width: 70%;
        }
</style>
@endsection



@section('content')
@include('common.header')
@include('common.menubuttons')
<div id="landing-view">
    <div class="container-fluid">
		<div class="clearfix"></div>
         <?php
            $total = 0;
            ?>
		<div class="d-flex"
			style="width:100%;margin-bottom: 5px; margin-top:5px">
			<div class="col-md-7 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Manual Record</h2>
			</div>
            <div class="col-md-3 pl-0 align-self-center" style="">
				<p style="margin-bottom: 0; font-size:13px; font-weight:bold;"> {{ $location->name }}</p>
                <p style="margin-bottom: 0;font-size:13px;font-weight:bold;"> {{ $location->systemid }}</p>
                <p style="margin-bottom: 0;font-size:13px;font-weight:bold;">{{ $time }}</p>
			</div>

            <div class="col-md-1 pl-0 align-self-center" style="justify-content:flex-end">

                <p style="margin-bottom: 0;font-size:13px;font-weight:bold;">Total {{ $total }}</p>
			</div> <?php
            $total = 0;
            ?>

                <div class="col-md-1 d-flex pr-0"
					style="justify-content:flex-end">
                    <button class="btn btn-success sellerbutton mr-0 btn-sq-lg bg-confirm-button"
					onclick=""
					style="margin-bottom:0 !important;border-radius:10px; font-size:14px;">Confirm
				</button>


			</div>
		</div>
         <div style="margin-top: 0">
        <table border="0" cellpadding="0" cellspacing="0" class="table " id="eodsummarylistd" style="margin-top: 0px; width:100%">
            <thead class="thead-dark"  >
            <tr id="table-th" style="border-style: none">
                <th valign="middle" class="text-center" style="width:3%;">Pump</th>
                <th valign="middle" class="text-center" style="width:3%;">Nozzle</th>
                <th valign="middle" class="text-left" style="width:30%;">Product Name</th>
                <th valign="middle" class="text-center" style="width:10%;">Start</th>
                <th valign="middle" class="text-center" style="width:3%;"></th>
                <th valign="middle" class="text-center" style="width:10%;">End</th>
                <th valign="middle" class="text-center" style="width:3%;"></th>
                <th valign="middle" class="text-center" style="width:5%;">Difference</th>
                <th valign="middle" class="text-center" style="width:5%;">Price</th>
                <th valign="middle" class="text-center" style="width:4%;">Amount</th>

            </tr>
            </thead>
            <tbody>

            @for ($i= 1;$i < 21; $i++)

                    @for ($n=1; $n<7; $n++)
                     <tr class="table-td">
                        <td class="text-center" style="border-style: none;width:3%;">

                            {{ $i }}

                        </td>
                        <td class="text-center" style="border-style: none;width:3%;">
                        {{ $n}}
                        </td>
                        <td class="text-left" style="border-style: none;width:30%;">
                            Prod-{{ \Str::random(8) }}
                        </td>
                        <td class="text-center" style="border-style: none;width:7%;">
                        {{ 3749393743+ $i }}
                        </td>
                        <td class="text-center" style="border-style: none;width:3%;">

                            <img src="{{ asset('images/bluecrab_50x50.png') }}" alt="no-I" height="25" width="25">

                        </td>
                        <td class="text-center" style="border-style: none;width:7%;">
                            {{ 3749393743+ $i }}
                        </td>
                        <td class="text-center" style="border-style: none;width:3%;">

                            <img src="{{ asset('images/bluecrab_50x50.png') }}" alt="no-I" height="25" width="25">

                        </td>
                        <td class="text-center" style="border-style: none;width:5%;">
                            {{ $i }}
                        </td>
                        <td class="text-center" style="border-style: none;width:5%;">
                            {{  number_format($i * 400,2)}}
                        </td>
                        <td class="text-right" style="border-style: none;width:4%;">
                             999,999.99
                        </td>
                    </tr>
                    @endfor


                <?php $total+=number_format( 999,999.99,2 ); ?>
            @endfor
            </tbody>
         </table>
         </div>
	</div>
    <script>
$(document).ready(function() {
    $('#eodsummarylistd').dataTable({
        // "aLengthMenu": [[10, 50, 75, -1], [10, 25, 50, 100]],
        // "iDisplayLength": 10,
        // 'aoColumnDefs': [{
        // 'bSortable': false,
        // 'aTargets': ['nosort']
    // }]
    });
} );
</script>

@endsection
@extends('common.footer')
