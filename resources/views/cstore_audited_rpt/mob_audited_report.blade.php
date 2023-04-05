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
button> 1:focus{
        outline:  none !important;
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

.slim-cell {
	padding-top: 2px !important;
	padding-bottom: 2px !important;
}

.pd_column {
	padding-top: 10px;
}

tr {
	height: 40px
}

.num_td{
	text-align: left;
}
.value-button {
	display: inline-block;
	font-size: 24px;
	line-height: 21px;
	text-align: center;
	vertical-align: middle;
	background: #fff;
	-webkit-touch-callout: none;
	-webkit-user-select: none;
	-khtml-user-select: none;
	-moz-user-select: none;
	-ms-user-select: none;
	user-select: none;
	text-align: center;
	text-align: center;
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
.value-button {
	cursor:pointer;
}

#buttons{
	position: fixed;
	left: 0;
	bottom: 0;

	color: white;
	text-align: center;
    display:flex;
    width: 100%;
    align-items: center;
    justify-content: space-evenly;
}
.bnt-primary{
    border-radius: 10px !important;
    background-color: rgb(17, 17, 253);
    color: #fff;
    padding: 3px;
}
#form1{

    height: 40px;
    margin: 0 auto;
    border: none;
     text-align: center;
     font-size: 18px;
     font-weight: bolder;
    color:rgb(12, 10, 10);
    background-color: #c9cec9;;
    padding:10px;
    border-radius: 10px;
    margin:10px 0;
}
 header{

        position: absolute;


    }

.flex-container {
  display: flex;
  flex-wrap: nowrap;
  justify-content: space-between;

  align-items: flex-end
}

.flex-container > div {
  font-size: 30px;
  width: 100%;
  height: 50%;
}
h5{
    font-weight: bold;
}
.child{
    position: fixed;
     background: #333;
        padding: 10px;
        color: #fff;
        top: 0;
        left: 0;
        right: 0;
        width: 100%;
}
section{
    margin-top:130px;
}
.qty{
    font-size: 30px !important;
}
</style>

@endsection


@include('common/mobile/mob_header')
@section('content')

<div id="landing-view">
    <div class="container-fluid">
	    <div class="clearfix"></div>
        <header >
            <div class="child">
                <div class="d-flex justify-content-between">
                    <h4 class="text-bold ">Audited Report</h4>
                    <img src="{{ asset('images/times_50x50.png') }}"
						alt="times"
						style="width:25px;height:25px;"
						onclick="window.open('{{ route('logout') }}')">
                </div>
                <div class="form-outline SearchBar">
                    <input type="search" id="form1"
                        class="form-control" placeholder="Search"
                        aria-label="Search" />
                </div>
            </div>
        </header>
        <section class="">
            @foreach ($audited_report_list as $key =>$product)

            <div class="flex-container ">

                @php
                    $img_src = '/images/product/' .
                            $product->systemid . '/thumb/' .
                            $product->thumbnail_1;
                    $path = public_path($img_src);
                @endphp
                <div style="width: 20% !important;">
                    @if (!empty($product->thumbnail_1) && file_exists($path))
                        <img src="{{ asset($img_src) }}"
						alt="imf" style="height:40px; width:40px;
						object-fit:cover; margin-right:10px; " class="">
                    @endif


                </div>
                <div style="">
					<h5 class="text-bold text-left">
						{{$product->name }}
					</h5>
                </div>


                <div class="" style="width: 35% !important;">
                    <h5 class="text-bold text-center qty"
						onclick="increase('{{ $key +1 }}')"
						id="qty_{{ $key +1 }}">
						{{number_format($product->Iqty)}}
					</h5>
                </div>

                <div class=""  style="width: 10% !important; ">
                    <button
                        class="mr-0 text-center"
						style="height:60px; width:25px; border-radius:10px;
                        background:#add8e6;color:#fff; outline:none !important; 
						border:1px solid #add8e6;"
						onclick="decrease('{{ $key +1 }}')"
						id="btn_{{ $key +1 }}"> -
                    </button>
                </div>
            </div>
            <hr class="solid">
            @endforeach
        </section>
        <div id="buttons" class="mb-2 m-auto">

            <button
                class="btn btn-success"
                style="height:60px; width:165px; border-radius:10px;
				background:#006ea1"
                id="fulltank-rl" >
                <span style="font-size:21px; font-weight:bolder;">
                Confirm
                </span>
            </button>

            <button class="btn btn-success"
                style="height:60px; width:168px; border-radius:10px !important;
				background: linear-gradient(#569331,#005668);"
                id="">
                <span style="font-size:21px !important; font-weight:bolder;">
                Scan
                </span>
            </button>
        </div>
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
    let dataLength = "{{ count($audited_report_list) +1 }}"

    for (let i = 1; i < dataLength ; i++) {
        var num_element = document.getElementById('number_'+i);
        var diff = document.getElementById('diff_'+i);
        var qty = document.getElementById('qty_'+i);
		var value = parseFloat(num_element.value);

        diff.textContent = num_element.value - qty.textContent;
        // console.log(diff.textContent)
    }
});

$.ajaxSetup({
	headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
});

var location_id = "{{$location->id}}";
var tablestockin ={};
var isConfirmEnabled = 0;

var tableData = {};


function increase(id) {
	qty = document.getElementById('qty_'+id);
	qty.textContent = parseInt(qty.textContent )+ 1;
}


function decrease(id) {
	var qty = document.getElementById('qty_'+id);
    qty.textContent = qty.textContent -1 < 0 ? 0 : parseInt(qty.textContent )- 1 ;
}

function changeValueOnBlur(id) {
	x = 0;
	ele = document.querySelectorAll('.number');
	ele.forEach( (e) => x += parseInt(e.value));
	isConfirmEnabled = x;

	var num_element = document.getElementById('number_'+id)
	var diff = document.getElementById('diff_'+id);
	var qty = document.getElementById('qty_'+id);

   return diff.textContent = parseFloat(num_element.value) - qty.textContent;
}

setInterval(() => {
	if (isConfirmEnabled > 0) {
		$("#confirm_update").removeAttr('disabled');
		$("#confirm_update").css('background','linear-gradient(#0447af,#3682f8)');
		$("#confirm_update").css('cursor','pointer');
	} else {
		$("#confirm_update").attr('disabled', true);
		$("#confirm_update").css('background','gray');
		$("#confirm_update").css('cursor','not-allowed');
	}
}, 1500);

function update_quantity() {
	const btn = document.getElementById('confirm');

	btn.addEventListener('click', function onClick() {
	btn.style.backgroundColor = '#A8A8A8';
	btn.style.color = 'white';
	});
	let dataLength = "{{ count($audited_report_list) +1 }}"
	console.log(parseInt(dataLength));
	let container = [];
	let product = {}

	for(let i=1; i < parseInt(dataLength); i++) {
		 let id = document.getElementById("product_"+i);
		 let qty = document.getElementById('qty_'+i);
		 var num_element = document.getElementById('number_'+i);
		  var diff = document.getElementById('diff_'+i);

		 product.product_id = id.textContent;
		 product.qty = qty.textContent;
		 product.audited_qty = parseFloat(num_element.value);
		 product.difference = diff.textContent;
		 container.push(product);
		 product ={};

	}
  console.log(container)
	if (container.length < 1)
		return;

	$.ajax({
	  url: "{{route('update_audited_notes')}}",
	  type: "POST",
	  'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
	  data: {container},
	  cache: false,
		 success: function(dataResult){
		  //$("#productResponse").html(dataResult);
		messageModal(`Audited note created successfully`);
		tablestockin.ajax.reload();
	  }
	});
}
</script>
@endsection

@endsection

