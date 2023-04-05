<?php $__env->startSection('styles'); ?>

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
	style="width:100%;padding-top:0;height:70px;margin-top:5px;margin-bottom:5px">
	<div class="col-md-5" style="align-self:center">
		<h2 class="mb-0 pt-0">
			Location Product: Product Ledger
		</h2>
	</div>

	<div class="col-md-1" style="align-self:center">
	<?php if(!empty($product->thumbnail_1) && file_exists(public_path("/images/product/".$product->systemid."/thumb/".$product->thumbnail_1))): ?>

		<img src="/images/product/<?php echo e($product->systemid); ?>/thumb/<?php echo e($product->thumbnail_1); ?>"
			alt="Logo" width="70px" height="70px" alt="Logo"
			style="object-fit:contain;float:right;margin-left:0;margin-top:0;">
	<?php endif; ?>
	</div>

	<div class="col-md-4" style="align-self:center;float:left;padding-left:0">
		<h4 style="margin-bottom:0px;padding-top: 0;line-height:1.5;">
			<?php if($product->name??""): ?>
				<?php echo e($product->name??""); ?>

			<?php else: ?>
				Product Name <?php endif; ?>
		</h4>
		<p style="font-size:18px; margin-bottom:0"> <?php echo e($product->systemid??""); ?></p>
	</div>
	<div class="col-md-3" style="float: right;">
	</div>
	</div>

	<div class="table-responsive mb-5" style="overflow-x: hidden;">
	<table class="table table-bordered" id="ogProductLeger" style="width: 100%;">
	<thead class="thead-dark">
		<tr>
		<th class="text-center" style="width:30px;">No</th>
		<th class="text-center" style="width:200px;">Document&nbsp;No</th>
		<th class="text-center" style="width:150px">Type</th>
		<th class="text-center" style="width:120px">Last&nbsp;Update</th>
		<th class="text-left" style="width:auto;">Location</th>
		<th class="text-center" style="width:80px">Cost</th>
		<th class="text-center" style="width:80px">Qty</th>
		</tr>
	</thead>

	<!--
	Types of ledger data (table names):
	1. opos_receiptproduct
	2. stockreportproduct
	3. opos_refund
	 -->

	<tbody>

	</tbody>
	</table>
	</div>

	<div class="modal fade" id="eodModal_1" tabindex="-1" role="dialog"
	style="overflow:auto;" aria-hidden="true">
	<div class="modal-dialog modal-dialog-centered mw-75"
		style="width:370px" role="document">
	<div id="recipt-model-div" class="modal-content bg-white"></div>
  </div>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
<div id="productResponce"></div>
<div id="response"></div>

<div class="modal fade" id="normalCostModal"  tabindex="-1"
	role="dialog"  aria-hidden="true">

	<div class="modal-dialog modal-dialog-centered modal-md  mw-75 w-50"
		role="document">
		<div class="modal-content modal-inside bg-purplelobster" >
			<div class="modal-header">
				<h4 id="qty_cost_tbl" style="margin-bottom:0">
					Cost Distribution
				</h4>
			</div>
			<div class="modal-body">
				<table class="table table-bordered align-content-center"
					id="qty_cost_tbl" style="width:100%">
					<thead class="thead-dark">
						<tr style="line-height:18px">
							<th>Cost</th>
							<th>Qty</th>
						</tr>
					</thead>
					<tbody class="tablebody" id="qty_dist_tbody">
						<tr class="text-center" style="line-height:18px">
							<td style="background: white;">
								0.00
							</td>
							<td style="background: white;">
								2
							</td>
						</tr>
						<tr class="text-center" style="line-height:18px">
							<td style="background: white;text-align: center;">
								1.50
							</td>
							<td style="background: white;text-align: center;">
								3
							</td>
						</tr>
					</tbody>
				</table>
			</div>
		</div>
	</div>
</div>


<script type="text/javascript">
    function show_cost_breakdown(receipt_id, product_id)
    {
        $.ajax({
            url: "/local_price/proledger/cost-distribution/"+receipt_id+"/"+product_id,
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
                $("#normalCostModal").modal('show');
            },
            error: function (e) {
                $('#responseeod').html(e);
                $("#msgModal").modal('show');
            }
        });
    }

    function showStockOutCostDist(stockreport_id) {
        $.ajax({
            url: "/stocking/locprod/proledger/stockout/cost-distribution/"+stockreport_id,
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
                $("#normalCostModal").modal('show');
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


    var tableData = {};

    var locProdProductTable = $('#ogProductLeger').DataTable({
        "processing": true,
        "serverSide": true,
        "autoWidth": false,
        "ajax": {
            "url": "<?php echo e(route('stocking.showLocationProductDatatable',$product->systemid)); ?>",
            "type": "POST",
            data: function (d) {
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
        "order": [0, 'desc'],
        "columnDefs": [
           /*  {"width": "5px", "targets": [0]},
            {"width": "30px", "targets": [1]},
            {"width": "60px", "targets": [2]},
            {"width": "60px", "targets": [3]},
            {"width": "60px", "targets": [4]},
            {"width": "40px", "targets": [5]},
            {"width": "20px", "targets": [6]}, */

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

<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/inv_stockmgmt/productledger.blade.php ENDPATH**/ ?>