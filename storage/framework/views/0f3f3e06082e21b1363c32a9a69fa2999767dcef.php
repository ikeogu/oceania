<?php $__env->startSection('styles'); ?>

<script type="text/javascript" src="<?php echo e(asset('js/console_logging.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>

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
</style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<div class="container-fluid">
<div class="row"
	style="width:100%;padding-top:0;height:70px;margin-top:5px;margin-bottom:5px;margin-left:0;margin-right:0">
	<div class="col-md-5 align-self-center pl-0">
		<h2 class="mb-0 pt-0">
			Open Item: Product Ledger
		</h2>
	</div>

	<div class="col-md-1" style="align-self:center">
	<?php if(!empty($product->thumbnail_1)): ?>
		<img src="/images/product/<?php echo e($product->systemid); ?>/thumb/<?php echo e($product->thumbnail_1); ?>"
			alt="Logo" width="70px" height="70px" alt="Logo"
			style="border-radius:10px;object-fit:contain;float:right;">
	<?php endif; ?>
	</div>

	<div class="col-md-4" style="align-self:center;float:left;padding-left:0">
       <h4 style="margin-bottom:0px;padding-top: 0;line-height:1.5;">
			<?php if($product->name??""): ?>
				<?php echo e($product->name??""); ?>

			<?php else: ?>
				Product Name
			<?php endif; ?>
		</h4>
       <p style="font-size:18px;margin-bottom:0"><?php echo e($product->systemid??""); ?></p>
	</div>
	<div class="col-md-3" style="float: right;">
	</div>
	</div>

	<div class="table-responsive mb-5" style="overflow-x: hidden;">
	<table class="table table-bordered" id="ogProductLeger" style="width: 100%;">
		<thead class="thead-dark">
			<tr style="line-height:20px">
				<th class="text-center" style="width:30px;">No</th>
				<th class="text-center" style="width:160px;">Document&nbsp;No</th>
				<th class="text-center" style="width: 100px">Type</th>
				<th class="text-center" style="width: 120px">Last&nbsp;Update</th>
				<th class="text-left" style="width: auto;">Location</th>
	            <th class="text-center" style="width: 80px;">Cost</th>
				<th class="text-center" style="width: 80px">Qty</th>
			</tr>
		</thead>
		<tbody>

        <!--
        Types of ledger data (table names):
        1. opos_receiptproduct
        2. stockreportproduct
        3. opos_refund
         -->

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
</div>

<div class="modal fade" id="cos_dist_modal"  tabindex="1"
		role="dialog"  aria-hidden="true">

	<div class="modal-dialog modal-dialog-centered modal-md  mw-75 w-50"
		role="document">
		<div class="modal-content modal-inside bg-purplelobster" style="z-index: auto;">
			<div class="modal-header" style="border:0">
				<h4 id="qty_cost_tbl">
					Cost Distribution
				</h4>
			</div>
			<div class="modal-body" style="padding-top: 0px;padding-bottom: 0px;">
				<table class="table table-bordered align-content-center"
					id="qty_cost_tbl" style="width:100%">
					<thead class="thead-dark">
						<tr>
							<th style="">Cost</th>
							<th style="">Qty</th>
						</tr>
					</thead>
					<tbody class="tablebody" id="qty_dist_tbody">
						<!-- <tr>
							<td style="background: white;text-align: center;" nowrap>
								<a href="#" style="text-decoration:none;">
									1110000000758
								</a>
							</td>
							<td style="background: white;text-align: center;" nowrap>
								2
							</td>
						</tr>
						<tr>
							<td style="background: white;text-align: center;" nowrap>
								<a href="#" style="text-decoration:none;">
									1110000000757
								</a>
							</td>
							<td style="background: white;text-align: center;" nowrap>
								3
							</td>
						</tr> -->
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>

<input id='receiptid' type='hidden' />

<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<div id="productResponce"></div>
<div id="response"></div>

<script type="text/javascript">
    $.ajaxSetup({
        headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')}
    });
	 function showCostDist(receipt_id, product_id)
	{
		$.ajax({
            url: "/openitem/proledger/cost-distribution/"+receipt_id+"/"+product_id,
            // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'get',
            success: function (response) {
                var rec = response;
				var html = "";
				rec.forEach(el => {
					html += "<tr>";
					html += " 	<td style=\"background: white;text-align: center;\" nowrap>";
					html += "			"+el.cost+"";
					html +=	"	</td>";
					html +=	"	<td style=\"background: white;text-align: center;\" nowrap>";
					html +=	"	  "+el.qty_taken+" ";
					html += "	</td> ";
					html += "</tr>";
				});
				$('#qty_dist_tbody').html(html);
                $("#cos_dist_modal").modal('show');
            },
            error: function (e) {
                $('#responseeod').html(e);
                $("#msgModal").modal('show');
            }
        });
	}

	function showStockOutCostDist(stockreport_id) {
		$.ajax({
            url: "/openitem/proledger/stockout/cost-distribution/"+stockreport_id,
            // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'get',
            success: function (response) {
                var rec = response;
				var html = "";
				rec.forEach(el => {
					html += "<tr>";
					html += " 	<td style=\"background: white;text-align: center;\" nowrap>";
					html += "			"+el.cost+"";
					html +=	"	</td>";
					html +=	"	<td style=\"background: white;text-align: center;\" nowrap>";
					html +=	"	  "+el.qty_taken+" ";
					html += "	</td> ";
					html += "</tr>";
				});
				$('#qty_dist_tbody').html(html);
                $("#cos_dist_modal").modal('show');
            },
            error: function (e) {
                $('#responseeod').html(e);
                $("#msgModal").modal('show');
            }
        });
	}

	function showReceipt(id){
        $('#eodSummaryListModal').modal('hide').html();
        $('#optlistModal').modal('hide').html();
        $('#receiptoposModal').modal('hide');
        $.ajax({
            url: "/local_cabinet/eodReceiptPopup/"+id,
            // headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
            type: 'get',
            success: function (response) {
                // console.log(response);
                $('#recipt-model-div').html(response);
                $('#eodModal_1').modal('show');
            },
            error: function (e) {
                $('#responseeod').html(e);
                $("#msgModal").modal('show');
            }
        });
    }


	function void_receipt(id) {
		$('#receiptid').val(id);
		$('#voidreceiptmodal').modal('show');
	}

	function onConfirmReceiptVoid() {
		var receiptid = $('#receiptid').val();
		var reason_void = $('#reason_void').val();
		var dt = time_void = new Date();
		var months = ["JAN", "FEB", "MAR", "APR",
			"MAY", "JUN", "JUL",
			"AUG", "SEP", "OCT", "NOV", "DEC"];

		time_void = time_void.getDate() + " " + months[time_void.getMonth()] +
			" " + time_void.getFullYear().toString().substr(-2) +
			" " + time_void.getHours() + ":" + time_void.getMinutes() +
			":" + time_void.getSeconds();

		var dtstring = dt.getFullYear() + "-" + (dt.getMonth() + 1) +
			"-" + dt.getDate() + " " + dt.getHours() + ":" +
			dt.getMinutes() + ":" + dt.getSeconds();

		$("#void-stamp" + receiptid).show();
		$("#void-div" + receiptid).show();
		$("#void-time" + receiptid).html(time_void);
		$("#void-reason" + receiptid).html(reason_void);
		$.ajax({
			url: "<?php echo e(route('local_cabinet.receipt.void')); ?>",
			type: 'post',
			headers: {
				'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
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

    var tableData = {};

    var openItemProductTable = $('#ogProductLeger').DataTable({
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            "url": "<?php echo e(route('openitem.productLegder.datatable',$product->systemid)); ?>",
            "type": "POST",
            data: function (d) {
                d.search = $('input[type=search]').val();
                return $.extend(d, tableData);
            },
            'headers': {
                'X-CSRF-TOKEN': '<?php echo e(csrf_token()); ?>'
            },
        },
        columns: [
            {data: 'DT_RowIndex', name: 'DT_RowIndex'},
            {data: 'product_systemid', name: 'product_systemid'},
            {data: 'type', name: 'type'},
            {data: 'last_update', name: 'last_update'},
            {data: 'location', name: 'lacation'},
            {data: 'cost', name: 'cost'},
            {data: 'quantity', name: 'quantity'},
        ],

        "columnDefs": [

            {"className": "dt-center vt_middle", "targets": [2]},
            {"className": "dt-left vt_middle slim-cell", "targets": [4]},
            {"className": "dt-center vt_middle slim-cell", "targets": [0, 1, 3,5,6]},
            {"className": "vt_middle slim-cell", "targets": [2,0, 1, 3,5,6]},
            // {"className": "vt_middle slim-cell", "targets": [6]},
            {orderable: false, targets: [-1]},
        ],
        "drawCallback": function( settings ) {

        }
    });
</script>

</div>
<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/openitem/openitem_productledger.blade.php ENDPATH**/ ?>