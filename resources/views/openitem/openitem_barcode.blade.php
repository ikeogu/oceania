@extends('common.web')
@include('common.header')

@section('styles')

<style type="text/css">
.vt_middle {
	vertical-align:middle !important;
}

.btn-date{
	background-color: #FFF;
    border-color: #FFF;
	border-radius: 5px;
	width: 100%;
	height: 38px !important;
	text-align: center;
    vertical-align: middle;
	font-size: 16px;
}

.block_1 {
	float: right;
	padding-left: 10px;
}

#decrease {
	border-radius: 15px;
}

#increase {
	border-radius: 15px;
}
    input[type=number]::-webkit-inner-spin-button,
    input[type=number]::-webkit-outer-spin-button {
      -webkit-appearance: none;
      margin: 0;
    }
input#number {
	text-align: center;
	border: none;
	border-radius: 5px;
	background-color: #d4d3d36b !important;
	vertical-align: text-bottom;
}

.inside_qty {
	margin-top: -3px;
}

.minus_plus {
	cursor: pointer;
	font-size: 28px;
	font-weight: bold;
}

a:link {
	text-decoration: none !important;
}

#para_middle:first-letter {
	padding-left: 15%;
}
td > a.sellerbutton{
	float: none;
	margin-right: 0px;
}

.btn-primary.disabled, .btn-primary:disabled {
    color: #fff;
    background-color: #ccc;
    border-color: #ccc;
	cursor: not-allowed;
}

.date_table > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

.date_table > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

.date_table_fifostart > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
text-align: center;
}

.date_table_fifostart > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
cursor: pointer;
text-align: center;
}

.date_table_fifoexp > tbody > tr > th {
	font-size: 22px;
	color: white;
	background-color: rgba(255, 255, 255, 0.5);
}

.date_table_fifoexp > tbody > tr > td {
	color: #fff;
	font-weight: 600;
	border: unset;
	font-size: 20px;
	cursor: pointer;
}

.selected_date {
color: darkgreen !important;
	font-weight: 600 !important;
}

#Datepickk .d-table {
	display: -webkit-flex !important;
	display: -ms-flexbox !important;
	display: flex !important;
}
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter {
	color: #212529 !important;
}

.modal-backdrop {
  display: none;
}

.checkbox-item {
	appearance: none;
  background-color: #fff;
  margin: 0;
  font: inherit;
  color: currentColor;
  width: 1.15em;
  height: 1.15em;
  border: 0.15em solid currentColor;
  border-radius: 0.15em;
  transform: translateY(-0.075em);
}

/* The container */
.containerx {
  display: block;
  position: relative;
  padding-left: 35px;
  margin-bottom: 12px;
  cursor: pointer;
  font-size: 22px;
  -webkit-user-select: none;
  -moz-user-select: none;
  -ms-user-select: none;
  user-select: none;
}

/* Hide the browser's default checkbox */
.containerx input {
  position: absolute;
  opacity: 0;
  cursor: pointer;
  height: 0;
  width: 0;
}

/* Create a custom checkbox */
.checkmark {
  position: absolute;
  top: 0;
  left: 0;
  height: 25px;
  width: 25px;
  background-color: #eee;
}

/* On mouse-over, add a grey background color */
.containerx:hover input ~ .checkmark {
  background-color: #ccc;
}

/* When the checkbox is checked, add a blue background */
.containerx input:checked ~ .checkmark {
  background-color: #2196F3;
}

/* Create the checkmark/indicator (hidden when not checked) */
.checkmark:after {
  content: "";
  position: absolute;
  display: none;
}

/* Show the checkmark when checked */
.containerx input:checked ~ .checkmark:after {
  display: block;
}

/* Style the checkmark/indicator */
.containerx .checkmark:after {
  left: 9px;
  top: 5px;
  width: 5px;
  height: 10px;
  border: solid white;
  border-width: 0 3px 3px 0;
  -webkit-transform: rotate(45deg);
  -ms-transform: rotate(45deg);
  transform: rotate(45deg);
}

</style>

@endsection

@include('common.menubuttons')

@section('content')


<div class="modal fade" id="barcode_sku" tabindex="-1"
    role="dialog" aria-labelledby="productcontModallabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" >
	    <div class="modal-content">
	        <form style="margin-bottom:0"
				class="m-form  m-form--state m-form--label-align-right " >
	            <div class="modal-body">
	                <div class="m-form__content">
	                    <input type="hidden" id="is_main"/>
						<input type="hidden" id="modal-barcode_id"
							name="product_id" value="">
	                    <input  type="text" name="sku" id="modal-sku"
							class="form-control m-input" placeholder="SKU">
	                </div>
	            </div>
	        <!--end::Form-->
	        </form>
	    </div>
	</div>
</div>

<div class="modal fade" id="barcode_name" tabindex="-1"
    role="dialog" aria-labelledby="productcontModallabel"
	aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" >
	    <div class="modal-content">
	        <form style="margin-bottom:0"
				class="m-form  m-form--state m-form--label-align-right " >
	            <div class="modal-body">
	                <div class="m-form__content">
	                    <input type="hidden" id="is_main"/>
						<input type="hidden" id="modal-barcode_id"
							name="product_id" value="">
	                    <input  type="text" name="name" id="modal-name"
							class="form-control m-input"
							placeholder="Barcode Name">
	                </div>
	            </div>
	        <!--end::Form-->
	        </form>
	    </div>
	</div>
</div>

<div class="modal fade" id="group_barcode_modal0" tabindex="-1" role="dialog"
	 aria-labelledby="productcontModallabel" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered" role="document" style="max-width:600px;">

		<div class="modal-content bg-purplelobster">
			<div class="modal-header">
				<h3 class="modal-title">Group Barcode</h3>
			</div>
			<div class="modal-body">
				<div class="row">
					<div class="col-md-4" style="padding-right:0">
						<h5>Note:</h5>
						Enter or scan barcodes. Separate with semicolon(;) or Enter
						<div class="col-md-12 d-flex justify-content-center"
							 style="align-items:end;padding-top:80px;padding-left:0;padding-right:0">
							<button
								class="btn btn-primary save-barcode sellerbutton"
								id="save_barcodes"
								style="padding-left:9px">
								<span>Submit</span>
							</button>
						</div>
					</div>
					<div class="col-md-8" style="padding-top:10px">
					<textarea style="border-radius:5px;width:95%;"
						name="group_barcode"
						id="group_barcode_id"
						rows="10" cols="45"
						placeholder="&nbsp;&nbsp;&nbsp;Please Enter/Scan Barcode"></textarea>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>


<div class="modal fade" id="fillFields2" tabindex="-1" role="dialog">
	<div class="modal-dialog  modal-dialog-centered mw-75 w-50">

		<!-- Modal content-->
		<div class="modal-content  modal-inside bg-purplelobster">
			<div class="modal-header" style="border:none;">&nbsp;</div>
			<div class="modal-body text-center">
				<h5 class="mb-0" id="return_data2">
					Please fill all fields
				</h5>
			</div>
			<div class="modal-footer" style="border: none;">&nbsp;</div>
		</div>

	</div>
</div>

<div class="modal fade"  id="msgModal"  tabindex="-1"
	role="dialog" aria-labelledby="staffNameLabel"
	aria-hidden="true" style="text-align: center;z-index:5000" onclick="close_me(this)">

	<div class="modal-dialog modal-dialog-centered  mw-75 w-50"
		role="document" >
		<div class="modal-content modal-inside bg-purplelobster"
			style="width: 100%;
				 background-color: {{@$color}} !important" >
			<div class="modal-header" style="border:0">&nbsp;</div>
			<div class="modal-body text-center">
				<h5 class="modal-title text-white mb-0"
					id="statusModalLabel">
				</h5>
			</div>
			<div class="modal-footer"
				style="border-top:0 none;padding-left:0;padding-right:0;">
				&nbsp;
			</div>
		</div>
	</div>
</div>

<div class="modal fade" id="delete_barcode_modal" tabindex="-1"
	 role="dialog" aria-labelledby="staffNameLabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered  mw-75 w-50"
	 role="document">
	<div class="modal-content modal-inside bg-purplelobster">

		<div class="modal-header" style="border-width:0"></div>
		<div class="modal-body text-center">
			<h5 class="modal-title text-white">
				Do you want to permanently
				delete this Barcode?
			</h5>
		</div>
		<div class="modal-footer text-center" style="border-width:0">
			<div class="row" style="width:100%;">
				<input type="hidden" name="" id="" value="">
				<div class="col col-m-12 text-center">
					<button class="btn btn-primary primary-button b_code" id=""
							onclick="yesDelete(this.id)">Yes
					</button>
					<button class="btn btn-danger primary-button"
							onclick="noDelete()">No
					</button>
				</div>
			</div>
		</div>
	</div>
</div>
</div>

<div class="d-flex mt-0 p-0"
	 style="width:100%; margin-top:5px !important;
	 margin-bottom:5px !important">
	 <div class="col-md-1 align-self-center">

 		@if(!empty($product->thumbnail_1))
 			<div>
 				<img src="/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}"
 					 style="object-fit:contain;width:60px;height: 60px;margin-right:0;"/>
 			</div>
 		@else
 			<div>
 				<img style="width:60px;height: 60px;margin-right:0; border: 1px solid #e0e0e0;border-radius: 5px">
 			</div>
 		@endif
 	</div>
 	<div class="col-md-6 align-self-center">
 		<div class="row">
 			<div style="margin-left: -25px">
 				<h4 style="margin-bottom:0px;margin-top:0;">
 				@if(!empty($product->name))
 						{{$product->name}}
 				@else
 						Product Name
 				@endif
 				</h4>
 				<p class="mb-0">{{$system_id}}</p>
 			</div>
 		</div>
 	</div>

	<div class="col-sm-5 pl-0">
		<div style=" padding-left: 20px;">
			<div style="float: right;">
				<a href="#" data-toggle="modal"
					data-target="#group_barcode_modal0">
					<button class="btn btn-success sellerbutton mr-0" onclick="show_create_code_modal()"
							style="padding:0;margin-bottom:5px !important">
						<span>Group<br>Barcode</span>
					</button>
				</a>
			</div>
		</div>
	</div>
</div>

<div class="col-sm-12" style="padding-left:15px;padding-right:15px;">
	<table class="table table-bordered " id="tableinventorybarcode_default" style="width:100%;">
		<thead class="thead-dark">
		<tr class="">
			<th class="text-center">No.</th>
			<th class="text-left" style="padding-left:80px">Barcode</th>
			<th class="text-center">Display</th>
			<th class="text-center"></th>
		</tr>
		</thead>
		<tbody>
		</tbody>
	</table>
</div>

@endsection


<script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
<script src="{{ asset('js/bootstrap.js') }}"></script>
<script type="text/javascript">

function generate_barcode() {
	$.get("{{route('openitem.barcode.generate')}}", { p_id : "{{$system_id}}"}).done(function(res){
		//$("#matrix_button").attr('disabled','diabled');
		$('#res').html(res);
		main_table.ajax.reload();
	});
}

function show_create_code_modal(){
	$("#group_barcode_id").val('')
	$("#group_barcode_modal0").modal('show');
}

function select_barcode(barcode_id, product_id)
{
	var url = "{{ route('openitem.record.selected') }}";
	$.ajax({
		method: "post",
		url: url,
		data: {barcode_id: barcode_id, product_id: product_id},
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
	})
	.done((data) => {
		console.log("data", data);
		main_table.ajax.reload();
	})
	.fail((data) => {
		console.log("data", data)
	});
}

function delete_barcode(tag_id) {
	let bc = tag_id.replace('bc_', '')
	$("#delete_barcode_modal").modal("show");
	$('.btn.btn-primary.primary-button.b_code').attr("id",bc);

}

function yesDelete(barcode) {
	$.ajax({
		method: "post",
		url: "{{route('openitem.barcode.delete')}}",
		data: {barcode: barcode},
		'headers': {
			'X-CSRF-TOKEN': '{{ csrf_token() }}'
		},
	})
	.done((data) => {
		console.log("data", data);

		$("#delete_barcode_modal").modal("hide");
		main_table.ajax.reload();
		$("#statusModalLabel").html("Barcode deleted successfully");
		$("#msgModal").modal('show');

		setTimeout(function () {
			$('#msgModal').modal('hide');
		}, 2000);
	})
	.fail((data) => {
		console.log("data", data)
	});
}

var url =  "{{route('openitem.barcode.table.show', request()->systemid )}}"
function load_table(url) {
	var tableData = {};
	var prd = {
		'prd_id': '{{$product->id}}'
	}
	main_table = $('#tableinventorybarcode_default').DataTable({
		"processing": true,
		"serverSide": true,
		"autoWidth": false,
		"ajax": {
			"url": url,
			"type": "POST",
			data: function (d) {
				return $.extend(d, tableData, prd);
			},
			'headers': {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
		},

		columns: [
			{data: 'DT_RowIndex', name: 'DT_RowIndex'},
			{data: 'product_barcode', name: 'product_barcode'},
			{data: 'select', name: 'select', sorting: false},
			{data: 'action', name: 'action'},
		],
		"order": [0, 'asc'],
		"columnDefs": [
			{"className": "dt-center vt_middle", "targets": [0, 2]},
			{"className": "dt-left vt_middle", "targets": [1]},
			{"className": "dt-center vt_middle slim-cell", "targets": [2, 3]},
			{'width':'30px', 'targets':[0,2,3]},
			{orderable: false, targets: [-1]},
		],
    });
}


window.onload = function(){

};
$(document).ready(function () {

	$('#group_barcode_modal0').on('show.bs.modal', function (e) {
		console.log('showing group modal', $(this))
		$(this).off('show.bs.modal')
	})

	$('#save_barcodes').click(function(){

		// $("#msgModal").remove();
		var barcodes = $('textarea[name=group_barcode]').val();

		$.ajax({
			url: "{{route('openitem.barcode.save')}}",
			data: {
			   'barcodes' : barcodes,
			   'id' : '{{$product->id}}'
			  },
			type: 'POST',
			dataType: "json",
			'headers': {
				'X-CSRF-TOKEN': '{{ csrf_token() }}'
			},
			success: function (response) {
				if(response != 0){
					$("#return_data2").html(e.responseText);
					$('#fillFields2').modal('show');
				}
				$('#tableinventorybarcode_default').DataTable().ajax.reload(null, true);

				$('textarea[name=group_barcode]').val("")
				$('#group_barcode_modal0').modal('hide');
				$('#tableinventorybarcode_default').DataTable().ajax.reload(null, true);
				// $('textarea[name=group_barcode]').val("");
				// $('#group_barcode_modal0').modal('hide');
			},
			error: function (e) {
				$('#group_barcode_modal0').modal('hide');
				$("#return_data2").html(e.responseText);
				$('#fillFields2').modal('show');
				$('#tableinventorybarcode_default').DataTable().ajax.reload(null, true);
			}
		});

	});

	let url =  "{{route('openitem.barcode.table.show')}}"
	load_table(url)
});

</script>
