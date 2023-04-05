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
	color: black;
}

th, td {
	vertical-align: middle !important;
	text-align: center
}

label, .dataTables_info,
.dataTables_wrapper .dataTables_length,
.dataTables_wrapper .dataTables_filter,
.dataTables_wrapper .dataTables_info,
.dataTables_wrapper .dataTables_processing,
.dataTables_wrapper .dataTables_paginate {
	color: #000 !important;
}

.active_button {
	color: #ccc;
	border: 1px #ccc solid;
}
td{
    border:1px solid rgb(226, 223, 223) !important;
}

.active_button:hover,
.active_button:active,
.active_button_activated {
	background: transparent !important;
	color: #34dabb !important;
	border: 1px #34dabb solid !important;
	font-weight: bold;
}

.active_button_activated {
	background: transparent;
	color: #34dabb;
	border: 1px #34dabb solid;
	font-weight: bold;
}

.slim-cell {
	padding-top: 2px !important;
	padding-bottom: 2px !important;
}

.typewriter {
    text-align: right;
    border-radius: 3px;
    overflow: hidden; /* Ensures the content is not revealed until the animation */
    /* border-right: .15em solid black;*/ /* The typwriter cursor */
    white-space: nowrap; /* Keeps the content on a single line */
    margin: 0 auto; /* Gives that scrolling effect as the typing happens */
    letter-spacing: .0em; /* Adjust as needed */
    /* animation:
      typing 3.5s steps(40, end),
      blink-caret .75s step-end infinite;*/
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
ion-icon{
    cursor:pointer;
}

</style>
<?php $__env->stopSection(); ?>



<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<div id="landing-view" style="margin-left:15px;margin-right:15px">
    <div class="container-fluid pl-0">
		<div class="clearfix"></div>
		<div class="row"
			style="margin-bottom: 5px; margin-top:5px">
			<div class="col-md-4 pl-0 align-self-center" style="">
				<h2 style="margin-bottom: 0;"> Audited Report</h2>
			</div>
            <div class="col-md-3 pl-0 align-self-center" style="">
				<h5 style="margin-bottom: 0;"> <?php echo e($location->name); ?></h5>
                <h5 style="margin-bottom: 0;"> <?php echo e($location->systemid); ?></h5>
                <h5 style="margin-bottom: 0;"> Doc No: <?php echo e($docId); ?></h5>
			</div>
            <div class="col-md-3 pl-0 align-self-center" style="">
				<h5 style="margin-bottom: 0;"> <?php echo e($user->fullname); ?></h5>
                <h5 style="margin-bottom: 0;"> <?php echo e($user->systemid); ?></h5>
                <h5 style="margin-bottom: 0;"> <?php echo e($time); ?></h5>
			</div>

            <div class="row col-md-2 text-left m-0 pr-0">
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

		</div>
         <div style="margin-top: 0">
        <table border="0" cellpadding="0" cellspacing="0" class="table "
			id="eodsummarylistd" style="margin-top: 0px; width:100%">
            <thead class="thead-dark"  >
            <tr id="table-th" style="border-style: none">
                <th valign="middle" class="text-center" style="width:30px">No</th>
                <th valign="middle" class="text-center" style="width:7%;">Product ID</th>
				<th valign="middle" class="text-center" style="width:7%;">Barcode</th>
                <th valign="middle" class="text-left" style="">Product Name</th>

                <th valign="middle" class="text-center" style="width:7%;">Qty</th>
                <th valign="middle" class="text-center" style="width:7%;">Audited&nbsp;Qty</th>
                <th valign="middle" class="text-center" style="width:7%;">Difference</th>
                <th valign="middle" class="text-center" style="width:7%;">Stock In</th>
                <th valign="middle" class="text-center" style="width:7%;">Stock Out</th>

            </tr>
            </thead>
            <tbody>

			<?php $__currentLoopData = $list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key =>$product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <tr class="table-td">
                    <td class="text-center"
						style="border-style: none;;width:30px;">
                        <?php echo e($key+ 1); ?>

                    </td>
                    <td class="text-center"
						style="border-style: none;width:200px;" >
                        <span id="product_<?php echo e($key +1); ?>">
						<?php echo e($product->psystemid); ?>

						</span>
                    </td>
					<td class="text-center"
						style="border-style: none;width:200px;" >
                        <span id="product_<?php echo e($key +1); ?>">
						<?php echo e($product->barcode); ?>

						</span>
                    </td>
                    <td class="text-left"
						style="padding-bottom:2px;padding-top:2px;
							border-style: none;width:auto;">
						<?php
						$img_url = '/images/product/' . $product->psystemid .
							'/thumb/' . $product->thumbnail_1;
						$path = public_path().$img_url;

						Log::info('product->thumbnail_1='.$product->thumbnail_1);
						Log::info('path='.$path);
						Log::info('asset='.asset($img_url));

                        ?>
                        <?php if(!empty($product->thumbnail_1) &&
						file_exists($path)): ?>
                            <img src="<?php echo e(asset($img_url)); ?>" alt="imf"
							style="height:30px; width:30px;
							padding-top:0; padding-bottom:0;padding-right:10px;
							object-fit:contain; border-radius:5px;">
							<?php echo e($product->name); ?>

                        <?php else: ?>
                        <?php echo e($product->name); ?>

                        <?php endif; ?>
                    </td>
                    <td class="text-center"
						style="border-style: none;width:100px;"
						id="qty_<?php echo e($key+ 1); ?>">
                        <?php echo e(number_format($product->qty)); ?>

                    </td>
					<td class="text-center"
					 	style="border-style: none;width:100px;"
						id="qty_<?php echo e($key+ 1); ?>">
                        <?php echo e(number_format($product->audited_qty)); ?>

                    </td>
                    <td class="text-center"
						style="border-style: none;width:100px;">
                        <?php echo e(number_format($product->audited_qty - $product->qty)); ?>

                    </td>
					<td class="text-center"
						style="border-style: none;width:100px;">
                        <?php if(($product->audited_qty - $product->qty) >=0): ?>
                             <?php echo e(number_format($product->audited_qty - $product->qty)); ?>

                        <?php else: ?>
							0
                        <?php endif; ?>
                    </td>
					<td class="text-center"
						style="border-style: none;width:100px;">
						<?php if(($product->audited_qty - $product->qty) <=0): ?>
							<?php echo e(number_format($product->audited_qty - $product->qty)); ?>

                        <?php else: ?>
							0
                        <?php endif; ?>
                    </td>
                </tr>

            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </tbody>
         </table>
         </div>
	</div>

<?php $__env->startSection('script'); ?>
<script>
$(document).ready(function() {
    $('#eodsummarylistd').dataTable();
        $.ajaxSetup({
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
} );

function downLoadExcel(){

    $.ajax({
		url: "<?php echo e(route('audited_report.excel_download')); ?>",
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
            link.download = "AuditedReport.xlsx";
            link.click();
        },
        error: function(blob){
            console.log(blob);
        }
    });
}


</script>
<?php $__env->stopSection(); ?>

<?php $__env->stopSection(); ?>


<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/cstore_audited_rpt/cstore_audited_report_confirmed.blade.php ENDPATH**/ ?>