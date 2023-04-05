@extends('common.web')

@section('styles')
<script type="text/javascript" src="{{ asset('js/qz-tray.js') }}"></script>
<script type="text/javascript" src="{{ asset('js/opossum_qz.js') }}"></script>

<style>
.butns{
	display: none
}
th{
vertical-align: middle !important;
	text-align: center
}
td{
	vertical-align: middle !important;
}
.bg-primary:hover{
	color:white;
}
</style>

<div id="landing-view">
<!--white abalone-->
<style media="screen">
a:link{
	text-decoration: none!important;
}
@media (min-width: 1025px) {
	#ogProductLeger{
		table-layout: fixed;
	}
	.remarks {
		white-space: nowrap;
		overflow-x: hidden;
		text-overflow: ellipsis;
	}
}

.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate{
    color: black !important;
    font-weight: normal !important;
}

#void_stamp{
	font-size:100px;
	color:red;
	position:absolute;
	z-index:2;
	font-weight:500;
	margin-top:130px;
	margin-left:15%;
	transform:rotate(45deg);
	display:none;
}
.modal-inside .row{
    color: black!important;
    padding: unset!important;
}
.modal-body {
     padding: unset!important;
}
</style>
@endsection

@section('content')
        <div class="modal fade" id="evReceiptDetailModal" tabindex="-1" role="dialog">
            <div class="modal-dialog  modal-dialog-centered" style="width: 366px; margin-top: 0!important;margin-bottom: 0!important;">

                <!-- Modal content-->
                <div class="modal-content  modal-inside detail_view" >

                </div>

            </div>
        </div>
@include('common.header')
@include('common.menubuttons')

{{-- modal remarks --}}
<!--
<div class="modal fade" id="remarks_qty" tabindex="-1"
	role="dialog" aria-labelledby="productcontModallabel" aria-hidden="true">
<div class="modal-dialog modal-dialog-centered" role="document" >
	<div class="modal-content">

		<form  class="m-form  m-form--state m-form--label-align-right " >
			<div class="modal-body">
        		<div class="m-form__content">
                <input type="hidden" id="modal-item_id" name="item_id" value="">
          			<input type="hidden" id="modal-remark_type"
						name="remark_type" value="">
					<textarea id="modal-item_remark" placeholder="Remarks"
						name="remark" class="form-control m-input"
						rows="3"></textarea>
				</div>
			</div>
		</form>
	</div>
</div>
</div>
-->

<div class="row"
	style="padding-top:0;height:75px;margin-top:0px;margin-bottom:0">
	<div class="col-sm-6" style="align-self:center">
		<h2 class="mb-0 pt-0">
			Fuel Movement: Product Ledger: Sales
		</h2>
	</div>

	<div class="col-sm-1" style="align-self:center">
	@if (!empty($product->thumbnail_1))
		<img src="/images/product/{{$product->systemid}}/thumb/{{$product->thumbnail_1}}"
			alt="Logo" width="70px" height="70px" alt="Logo"
			style="object-fit:contain;float:right;margin-left:0;margin-top:0;">
	@endif
   </div>

   <div class="col-sm-4" style="align-self:center;float:left;padding-left:0">
       <h4 style="margin-bottom:0px;padding-top: 0;line-height:1.5;">@if($product->name??""){{$product->name??""}} @else Product Name @endif</h4>
       <p style="font-size:18px;margin-bottom:0">{{$product->systemid??""}}</p>
   </div>
      <div class="col-sm-3" style="float: right;">
      </div>
	</div>
  <div class="table-responsive mb-5" style="overflow-x: hidden;">
	<table class="table table-bordered" id="ogProductLeger" style="width: 100%;">
		<thead class="thead-dark">
          <tr>
              <th class="text-center" style="width:30px;text-align: center;">No</th>
              <th class="text-center" style="width:15%;text-align: center;">Document&nbsp;No</th>
              <th class="text-center" style="width: 11%">Type</th>
              <th class="text-center" style="width: 120px" nowrap>Last&nbsp;Update</th>
              <th class="text-center" style="width: auto;">Location</th>
              {{-- <th class="text-center" style="width: 95px;">Actual Litre</th> --}}
              <th class="text-center" style="width: 80px">Litre&nbsp;(&ell;)</th>
              <!--th class="text-left" style="width: 36%">Remarks</th-->

          </tr>
		</thead>
		<tbody>

		</tbody>
	</table>
  </div>
  <div class="modal fade" id="eodModal_1" tabindex="-1" role="dialog"
  style="overflow:auto;" aria-hidden="true">
  <div class="modal-dialog modal-dialog-centered mw-75"
      style="width:370px" role="document">
      <div id="recipt-model-div" class="modal-content bg-white">
      </div>
  </div>
</div>

 <div class="modal fade" id="voidreceiptmodal" tabindex="-1" role="dialog">
        <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
            <div class="modal-content modal-inside bg-purplelobster">
                <div style="border:0" class="modal-header"></div>
                <div class="modal-body text-center">
                    <h5 class="modal-title text-white" id="logoutModalLabel">
                        Do you want to void the receipt?
                    </h5>
                    <br/><input type="hidden" id="receiptid" name="receiptid">
                    <textarea placeholder="Reason for void receipt" rows='4'
                              id="reason_void" class="form-control"></textarea>
                </div>
                <div class="modal-footer"
                     style="border-top:0 none; padding:0;padding-bottom: 15px;">
                    <div class="row" style="width: 100%; padding:0">
                        <div class="col col-m-12 text-center">
                            <a class="btn btn-primary"
                               href="javascript:void(0)" style="width:100px"
                               data-dismiss="modal"
                               onclick="onConfirmReceiptVoid()">
                                Confirm
                            </a>
                            <button type="button" class="btn btn-danger"
                                    data-dismiss="modal" style="width:100px">
                                Cancel
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

<input id='receiptid' type='hidden' />
{{--@include('inventory.inventoryqtypdtlocation')--}}
@endsection
@section('script')
<script src="{{asset('js/number_format.js')}}"></script>
<div id="productResponce"></div>
<div id="response"></div>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
    var tableData = { };


    var table =$('#ogProductLeger').DataTable({

        "processing": false,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            "url": "{{route("fuel_movement.fuel-mouv-receipts-table",[$id,$date])}}",
            "type": "get",
            data: function (d) {
                return $.extend(d, tableData);
            },
            'headers': {
                'X-CSRF-TOKEN': '{{ csrf_token() }}'
            }
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'systemid', name: 'systemid'},
            {data: 'type', name: 'type'},
            {data: 'date', name: 'date'},
            {data: 'location', name: 'location'},
            {data: 'qty', name: 'qty', render: function (data) {
                return number_format(data,2);
                }
                },
        ],
        createdRow: (row, data, dataIndex, cells) => {
            $(cells[1]).css('background-color', data.status_color);
            $(cells[1]).css('color', "white")
        },
        "columnDefs": [
            {"width": "5%", "targets": [0]},
            {"width": "17%", "targets": 1},
            {"width": "15%", "targets": [2,3,5]},
            {"width": "50%", "targets": [4]},
            {"className": "dt-center vt_middle", "targets": [0, 1, 2, 3,4,5]},
            {"className": "vt_middle", "targets": []},
            {orderable: false, targets: [-1]},
        ],

    });

function showReceipt(data, is_ft, is_oew){
    var url = "{{route('fuel.envReceipt')}}";
    if(is_ft){
        url ="{{route('fulltank.envReceipt')}}"
    } else if (is_oew) {
    		url ="{{route('oew.get_receiptlist')}}"
    }

    $.ajax({
        method: "post",
        url: url,
        data: {id: data}
    }).done((data) => {
        $(".detail_view").html(data);
        $("#evReceiptDetailModal").modal('show');
    })
        .fail((data) => {
            console.log("data", data)
        });
    }


		function refundMe(id, fuel, filled) {
			$.ajax({
					method: "post",
					url: "{{ route('oew.refund') }}",
					data: {
						id: id,
						filled: filled,
						fuel: fuel
					}
				}).done((data) => {
		      console.log("data", data)
					table.ajax.reload();
					localStorage.setItem('receipt_refunded', id);
					localStorage.setItem('receipt_refunded1', id);
					localStorage.removeItem('receipt_refunded')
					localStorage.removeItem('reload_for_fm_sales')
					localStorage.setItem("reload_for_fm_sales", "yes");

		      var oew_refund_btn = document.getElementById('oew_refund_btn');
		      $('#oew_refund_btn').removeClass('btn-warning');
		      $('#oew_refund_btn').css("background-color", "#3a3535");
		      $('#oew_refund_btn').attr('disabled', true);

				})
				.fail((data) => {
					console.log("data", data)
				});
		}



    function onConfirmReceiptVoid() {
            var receiptid = $('#receiptid').val();
            var reason_void = $('#reason_void').val();
            var dt = time_void = new Date();
            var months = ["JAN", "FEB", "MAR", "APR", "MAY", "JUN", "JUL",
                "AUG", "SEP", "OCT", "NOV", "DEC"];

            time_void = time_void.getDate() + " " + months[time_void.getMonth()] + " " + time_void.getFullYear().toString().substr(-2) + " " + time_void.getHours() + ":" + time_void.getMinutes() + ":" + time_void.getSeconds();

            var dtstring = dt.getFullYear() + "-" + (dt.getMonth() + 1) + "-" + dt.getDate() + " " + dt.getHours() + ":" + dt.getMinutes() + ":" + dt.getSeconds();

            $("#void-stamp" + receiptid).show();
            $("#void-div" + receiptid).show();
            $("#void-time" + receiptid).html(time_void);
            $("#void-reason" + receiptid).html(reason_void);
            $.ajax({
                url: "{{route('local_cabinet.receipt.void')}}",
                type: 'post',
                headers: {
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                data: {
                    "receiptid": receiptid,
                    "reason_void": reason_void,
                    "voitdatetime": dtstring
                },
                dataType: 'json',
                success: function (response) {
                    $("#void-stamp" + receiptid).show();
                    $("#void-div" + receiptid).show();
                    $("#void-time" + receiptid).html(time_void);
                    $("#void-reason" + receiptid).html(reason_void);
					$("#qty_l").text('0.00');
                }
            });
        }
        window.addEventListener('storage', (e) => {
        switch (e.key) {
            case "reload_ledger":
                table.ajax.reload();
			    localStorage.removeItem('reload_ledger')
                break;
	}
});

</script>
{{-- @include('settings.buttonpermission') --}}


</div>
@include('common.footer')
@endsection
