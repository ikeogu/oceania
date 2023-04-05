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
table, th, td {
  border: 0.3px solid rgb(243, 237, 237) !important;
  border-collapse: collapse !important;
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

input.number {
	text-align: center;
	border: none;
	border: 1px solid #e2dddd;
	margin: 0px;
	width: 90px;
	border-radius: 5px;
	height: 38px;
	border-radius: 5px;
	background-color: #d4d3d36b !important;
	vertical-align: text-bottom;
}


.form-group select.form-control {
  display: inline-block;
  width: 280px !important;
   padding:-1.2% !important;
   margin-bottom:-2.5% !important;
   font-size: 11px !important;
   margin-left:2% !important;

}
</style>
@endsection



@section('content')
@include('common.header')
@include('common.menubuttons')
<div id="landing-view">
    <div class="container-fluid">
		<div class="clearfix"></div>
		<div class="d-flex"
			style="width:100%;margin-bottom: 5px; margin-top:5px">
			<div class="col-md-10 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Product Mapping</h2>
			</div>
                <div class="col-md-2 d-flex pr-0"
					style="justify-content:flex-end">
                    <button class="btn btn-success bg-save  sellerbutton mr-0 btn-sq-lg"
					onclick=""
					style="margin-bottom:0 !important;border-radius:10px; font-size:14px;">Push
				</button>
			</div>
		</div>

        @if(!empty($pumps))

            <!--
                This uses 2 loops, onee for row the other for columns
                for when to loop to the next row
                and which index to begin the row loop at
            -->
            <?php $rowIndex=0; ?>
            @foreach($pumps as $k => $pump)

                 <div class="row">

                     <!--
                         This is a column loop, uses 2 variables
                         Where to start the column ($startCol) loop
                         and where to end it ($endCol)
                     -->
                     <?php
                        $startCol=$rowIndex;
                        $endCol=$startCol+3;
                        $rowIndex=$endCol;
                     ?>
                     @foreach($pumps as $col => $pump)

                            @if($col < $startCol)
                                <?php continue; ?>
                            @elseif ($col > $endCol)
                                <?php break; ?>
                            @elseif ($col >= $startCol && $col <= $endCol)
                                <div class="col-3 mb-5">
                                     <h5 class="mb-2" >
                                        Pump {{ $pump->pump_no }}
                                    </h5>
                                    @foreach($pump->nozzles as $nozzle)

                                    <form class="">

                                         <div class="form-group" style="">
                                            <label class="control-label">Nozzle {{ $nozzle->lpz_nozzle_no }} </label>
                                            <div class="">
                                                <select class="form-control"  id="select_pmp_{{$pump->pump_no}}_noz_{{ $nozzle->lpz_nozzle_no }}">
                                                    <option></option>
                                                    @foreach($products as $prd)
                                                        @if($prd->og_fuel_id == $nozzle->lpz_ogfuel_id)
                                                            <option
                                                                value="{{ $pump->pump_no }}_{{ $nozzle->lpz_nozzle_no }}_{{$prd->og_fuel_id}}"
                                                                {{ $prd->og_fuel_id == $nozzle->lpz_ogfuel_id ? 'selected' : '' }}>
                                                                {{$prd->name}}&ensp;{{number_format(($prd->price/100),2)}}
                                                            </option>
                                                        @else
                                                            <option value="{{ $pump->pump_no }}_{{ $nozzle->lpz_nozzle_no }}_{{$prd->og_fuel_id}}" style="">
                                                                {{$prd->name}}&nbsp;{{number_format(($prd->price/100),2)}}
                                                            </option>
                                                        @endif
                                                    @endforeach

                                                </select>
                                            </div>
                                        </div>
                                    </form>
                                    @endforeach
                                    @if(count($pump->nozzles) < 6)
                                        <?php
                                            $extra_nozzles = 6 - count($pump->nozzles);
                                        ?>
                                        @for($i=1; $i<=$extra_nozzles; $i++)

                                        <form class="">

                                             <div class="form-group" style="">
                                                <label class="control-label">Nozzle {{ count($pump->nozzles) + $i }} </label>
                                                <div class="">
                                                    <select class="form-control"  id="select_form">
                                                        @foreach($products as $prd)
                                                            @if($prd->og_fuel_id == $nozzle->lpz_ogfuel_id)
                                                                <option value="{{$prd->og_fuel_id}}" style="" {{ $prd->og_fuel_id == $nozzle->lpz_ogfuel_id ? 'selected' : '' }}>
                                                                    {{$prd->name}}
                                                                </option>
                                                            @else
                                                                <option value="{{$prd->og_fuel_id}}" style="">
                                                                    {{$prd->name}}
                                                                </option>
                                                            @endif
                                                        @endforeach

                                                    </select>
                                                </div>
                                            </div>
                                        </form>
                                        @endfor
                                    @endif
                                </div>
                            @endif
                    @endforeach
                 </div>

            @endforeach

        @endif

    </div>
</div>

<script type="text/javascript">
$("select[id^='select_pmp_']").change(function(){
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

    let nozzle_data = $(this).val().split("_");
    if (nozzle_data != undefined &&
        nozzle_data != null &&
        nozzle_data != '') {
            let pump_id = nozzle_data[0]
            let nozzle_no = nozzle_data[1]
            let ogfuel_id = nozzle_data[2]

            $.ajax({
                    method: "post",
                    url: "{{ route('product_mapping.save_mapping') }}",
                    data: {
                        pump_id: pump_id,
                        nozzle_no: nozzle_no,
                        ogfuel_id: ogfuel_id,
                    }
                }).done((data) => {
                    console.log('**mapping saved**', data)
                })
                .fail((data) => {
                    console.log('**mapping failed**', data)
                });
    }

})
</script>

@endsection
@extends('common.footer')
