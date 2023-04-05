<?php $__env->startSection('styles'); ?>

<script type="text/javascript" src="<?php echo e(asset('js/console_logging.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/qz-tray.js')); ?>"></script>
<script type="text/javascript" src="<?php echo e(asset('js/opossum_qz.js')); ?>"></script>

<style>
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
    color: black !important;
    font-weight: normal !important;
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

.dt-center.vt_middle.small-pad-y {
    padding-top: 0%;
    padding-bottom: 0%;
}
table ,tr,td{
    padding:0.29% !important;
}
</style>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="landing-view">
    <div class="container-fluid">
		<div class="clearfix"></div>
		<div class="row"
			style="width:100%;margin-bottom: 5px; margin-top:5px; height:70px; margin-left:6px;">
			<div class="col-md-2 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Returning Note</h2>
			</div>
             <div class="col-md-3 pl-0 align-self-center" style="">
				<p style="margin-bottom: 0; font-size:18px; font-weight:bold;"> <?php echo e($location->name); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($location->systemid); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> Doc No. <?php echo e($docId); ?></p>
			</div>
            <div class="col-md-3 pl-0 align-self-center" style="">
				<p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($user->fullname); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($user->systemid); ?></p>
                <p style="margin-bottom: 0;font-size:18px;font-weight:bold;"> <?php echo e($time); ?></p>
			</div>

			<div class="row col-md-2 text-left m-0 pr-0">
				<?php if(!empty($invoice_no)): ?>
                    <div class="col-md-5" style="">
                        <strong style="font-size:18px;">
                            <label class="mb-0">Invoice No:&nbsp;</label>
                            <span>  <?php echo e($invoice_no); ?></span>
                        </strong>
                    </div>
				<?php endif; ?>
            </div>

            <div style="display:inline;
                padding-left:208px;margin-bottom:20px" id="btnFetch2">
                    <button class="btn btn-success "
                    style="height:70px;width: 70px; border-radius:10px !important;"
                    id="fulltank-rl" style="color:white;" onclick="downLoadExcel()">
                    <span class="d-none spinner-border spinner-border-sm" role="status" aria-hidden="true" style="z-index:2; position: fixed; margin-top: 3px;
                    margin-left:14px"></span>
                    Excel
                </button>
            </div>
		</div>
         <div style="margin-top: 0;margin-right:5px; margin-left:5px;">
            <table border="0" cellpadding="0" cellspacing="0" class="table " id="return_note_table"
            style="margin-top: 0px; width:100%">
                <thead class="thead-dark"  >
		            <tr id="table-th" style="border-style: none">
		                <th valign="middle" style="width:5%;" class="text-center">No</th>
		                <th valign=""  style="width:15%;"  class="text-center">Barcode</th>
                        <th valign="middle">Product Name</th>
		                <th style="width:6%;" class="text-center">Qty</th>
		                <th valign="middle"  style="width:8%;"  class="text-center">Cost</th>
		            </tr>
                </thead>
                <tbody id="shows">
                    <?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $report): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                    <tr>
                        <td> <?php echo e($key + 1); ?></td>
                        <td class="text-center"><?php echo e($report->barcode); ?></td>
                        <td class="text-left" style="padding-bottom:2px;padding-top:2px;
                                border-style: none;width:auto;">
                            <?php
                            $img_url = '/images/product/' . $report->psystemid .
                                '/thumb/' . $report->thumbnail_1;
                            $path = public_path().$img_url;

                            Log::info('product->thumbnail_1='.$report->thumbnail_1);
                            Log::info('path='.$path);
                            Log::info('asset='.asset($img_url));

                            ?>
                            <?php if(!empty($report->thumbnail_1) &&
                                file_exists($path)): ?>
                                <img src="<?php echo e(asset($img_url)); ?>" alt="imf"
                                style="height:30px; width:30px;
                                padding-top:0; padding-bottom:0;padding-right:10px;
                                object-fit:contain; border-radius:5px;">
                                <?php echo e($report->name); ?>

                            <?php else: ?>
                            <?php echo e($report->name); ?>

                            <?php endif; ?>
                        </td>
                        <td class="text-center"> <?php echo e($report->qty); ?></td>
                        <td class="text-right"> <?php echo e(number_format($report->cost /100,2)); ?></td>

                    </tr>
                    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            	</tbody>
             </table>
         </div>
	</div>
<script src="<?php echo e(asset('js/number_format.js')); ?>"></script>
<script>

$(document).ready(function() {
    $('#return_note_table').dataTable();
    $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });

} );

function downLoadExcel(){

    $.ajax({
		url: "<?php echo e(route('returning.excel_download')); ?>",
		// headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		type: 'post',
        data: {
            doc_no: '<?php echo e($docId); ?>'
        },
		xhrFields: {
            responseType: 'blob'
        },
        success: function(response){
            var blob = new Blob([response]);
            var link = document.createElement('a');
            link.href = window.URL.createObjectURL(blob);
            link.download = "ReturningNote.xlsx";
            link.click();
        },
        error: function(blob){
            console.log(blob);
        }
    });
}
</script>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/returning/cstore_returning_note_confirmed.blade.php ENDPATH**/ ?>