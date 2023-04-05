@extends('common.web')
@section('styles')

<script type="text/javascript" src="{{ asset('js/console_logging.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/qz-tray.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/opossum_qz.js') }}"></script>

<style>
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

table.dataTable.order-column tbody tr>.sorting_1,
table.dataTable.order-column tbody tr>.sorting_2,
table.dataTable.order-column tbody tr>.sorting_3,
table.dataTable.display tbody tr>.sorting_1,
table.dataTable.display tbody tr>.sorting_2,
table.dataTable.display tbody tr>.sorting_3 {
    background-color: #fff !important;
}
</style>
@endsection

@section('content')
@include('common.header')
@include('common.menubuttons')

<div id="landing-view" style="display: block !important;">
    <!--div id="landing-content" style="width: 100%"-->
    <div class="container-fluid">
        <div class="clearfix"></div>
        <div class="row py-2 align-items-center" style="display:flex;height:75px">
            <div class="col" style="width:70%">
                <h2 style="margin-bottom: 0;">
                    Outdoor e-Wallet Provider Information 
                </h2>

            </div>
            <div class="col-md-2">
                <h5 style="margin-bottom:0"></h5>
                <h5 style="margin-bottom:0"></h5>
            </div>
            <div class="middle;col-md-3">
                <h5 style="margin-bottom:0;"></h5>
            </div>
            <div class="col-md-2 text-right">
                <h5 style="margin-bottom:0;"></h5>
            </div>
        </div>

        <div id="response"></div>
        <div id="responseeod"></div>
        <table class="table table-bordered display boxhead" id="eodsummarylistd" style="width:100%;">
            <thead class="thead-dark">
                <tr>
                    <th class="text-center" style="width:30px;">No.</th>
                    <th class="text-center" style="width:120px;">Logo</th>
                    <th class="" style="width:auto;">e-Wallet Name</th>
                    <th class="text-center" style="width:200px;">e-Wallet Merchant ID</th>
                    <th class="text-center nosort" style="width:100px;">Location ID</th>
             <!--        <th class="text-center" style="width:100px">Filled</th>
                    <th class="text-center" style="width:100px">Refund</th>
                    <th class="text-center" style="width:25px;"></th> -->
                </tr>
            </thead>

            <tbody>
                <tr>
                <td>{{ 1 }}</td>
                <td>{{ "" }}</td>
                <td class="text-left">{{ "S Pay Global" }}</td>
                <td>{{ "M100004540" }}</td>
                <td>{{ "1040000000143" }}</td>
<!--                 <td>{{ "0.00" }}</td>
                <td>{{ "1.00" }}</td>
                <td>
					<a href="javascript:void(0)"
						id="them"   
						target="_blank" xonclick=""
						style="pointer-events: none; cursor: default;"
						data-row ="1"class="btn btn-sm ">
						<img style="width:25px" src="images/bluecrab_50x50.png" alt="">
					</a>
				 --></td>
<!--                <td><button class="btn btn-success" id="them" onclick="open_self_service_receipt();">Open model</button></td> -->
            </tr>
            </tbody>
        </table>
    </div>
</div>


@section('script')
<script>

$(document).ready(function() {
    $('#eodsummarylistd').dataTable({
        "aLengthMenu": [[10, 50, 75, -1], [10, 25, 50, 100]],
        "iDisplayLength": 10,
        'aoColumnDefs': [{
        'bSortable': false,
        'aTargets': ['nosort']
    }]
    });
} );
</script>
@endsection
@extends('common.footer')
