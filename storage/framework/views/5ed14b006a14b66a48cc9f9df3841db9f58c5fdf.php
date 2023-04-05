<?php $__env->startSection('styles'); ?>



<div id="landing-view">
 <style type="text/css">
    .inside_qty{
       margin-top: -3px;
    }
    #prodstockreport tbody td{
        display: table-cell;
        vertical-align: inherit;
        padding-bottom: 2px !important;
        padding-top: 2px !important;
    }
    div.col-sm-3 p {
        margin-bottom: 0px;
    }

element.style {
}
label {
    display: inline-block;
    margin-bottom: 0.5rem;
}
*, *::before, *::after {
    box-sizing: border-box;
}
user agent stylesheet
label {
    cursor: default;
}
.dataTables_wrapper .dataTables_length, .dataTables_wrapper .dataTables_filter, .dataTables_wrapper .dataTables_info, .dataTables_wrapper .dataTables_processing, .dataTables_wrapper .dataTables_paginate {
    color: #333 !important;
}
 </style>
<?php $__env->stopSection(); ?>

<?php $__env->startSection('content'); ?>
<?php echo $__env->make('common.header', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php echo $__env->make('common.menubuttons', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>

<?php $__env->startSection('content'); ?>
<div class="container-fluid">
<div class="row py-2" style="padding-bottom:0px !important">
    <div class="col align-self-center" style="width:80%">
        <h2>
            <?php if($stockreport_data->srtype == 'stockin'): ?>
                Stock In
            <?php elseif($stockreport_data->srtype == 'stockout'): ?>
                Stock Out
            <?php elseif($stockreport_data->srtype == 'returned'): ?>
                Returned
            <?php elseif($stockreport_data->srtype == 'received'): ?>
                Received
            <?php elseif($stockreport_data->srtype == 'voided'): ?>
                Voided
            <?php elseif($stockreport_data->srtype == 'transfer'): ?>
                Transfer
            <?php elseif($stockreport_data->srtype == 'stocktake'): ?>
                Stock Take
            <?php elseif($stockreport_data->srtype == 'cforward'): ?>
                Carry Forward
            <?php elseif($stockreport_data->srtype == 'refundcp'): ?>
                Refund Cp
            <?php elseif($stockreport_data->srtype == 'daily_variance'): ?>
                Daily Variance
            <?php endif; ?>
        </h2>
    </div>
    <div class="col-sm-1" style="align-self:center">

    </div>
    <div class="col-sm-5" style="align-self:center;float:left;padding-left:0">
<h4 style="margin-bottom:0px;padding-top: 0;line-height:1.5;">Report ID : <?php echo e($stockreport_data->document_no); ?></h4>
    <p>Location : <?php echo e($stockreport_data->location); ?> </p>
    </div>
    <div class="col-sm-3" style="float: right;">
        <p>Staff Name: <?php echo e($stockreport_data->staff_name); ?></p>
        <p>Staff ID: <?php echo e($stockreport_data->staff_id); ?></p>
        <p>Date: <?php echo date('dMy H:i:s',strtotime($stockreport_data->last_update)); ?></p>
    </div>
</div>

<table class="table table-bordered" id="prodstockreport" style="width:100%;">
    <thead class="thead-dark">
    <tr>
        <th style="width:30px;text-align: center;">No</th>
        <th style="width:100px;text-align: center;">Product&nbsp;ID</th>
        <th>Product Name</th>
        <th style="text-align: center;">Colour</th>
        <th>Matrix</th>
        <th style="text-align: center;width:50px">Rack</th>
        <th style="text-align: center; width: 80px;"><?php if($stockreport_data->srtype == 'stockin'): ?> Qty In <?php else: ?> Qty Out <?php endif; ?></th>
    </tr>
    </thead>
    <tbody>

    <?php $__currentLoopData = $stockreport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $value): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
		<tr>
            <td style="width:30px;text-align: center;"><?php echo e($key+1); ?></td>
            <td style="width:100px;text-align: center;">
				<?php echo e($value->systemid); ?>

			</td>
            <td>
                <?php
                    $img_url = '/images/product/' . $value->systemid .
                        '/thumb/' . $value->thumbnail_1;
                    $path = public_path().$img_url;
                ?>
                <?php if(!empty($value->thumbnail_1) &&
                file_exists($path)): ?>
                    <img src="<?php echo e(asset($img_url)); ?>" alt="imf"
                    style="height:30px; width:30px;
                    padding-top:0; padding-bottom:0;padding-right:10px;
                    object-fit:contain; border-radius:5px;">
                    <?php echo e($value->name); ?>

                <?php else: ?>
                <?php echo e($value->name); ?>

                <?php endif; ?>
            </td>
            <td style="text-align: center;"><?php if(!empty($value->color)): ?> <div style="padding:10px 20px; background:<?php echo e($value->color); ?>"></div> <?php else: ?> - <?php endif; ?></td>
	    <td style="">  -</td>
		<td style="text-align:center;"><?php echo e($value->rack_no ?? '-'); ?></td>
        <td style="text-align: center;"><?php if($value->quantity): ?> <?php echo e($value->quantity); ?> <?php else: ?> - <?php endif; ?></td>
        </tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
</div>
</div>
<?php $__env->stopSection(); ?>
<?php $__env->startSection('script'); ?>
 <script type="text/javascript">
      $(document).ready(function () {
        // prodstockreportTable.draw();
            tableinventory =  $('#prodstockreport').DataTable({
              // "order": [[ 3, "desc" ]]
            });
    	<?php if($isWarehouse != 1): ?>
		tableinventory.column(5).visible(false);
	<?php endif; ?>
     });

   </script>

<?php echo $__env->make('common.footer', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?>
<?php $__env->stopSection(); ?>

<?php echo $__env->make('common.web', \Illuminate\Support\Arr::except(get_defined_vars(), ['__data', '__path']))->render(); ?><?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/inv_stockmgmt/inventorystockreport.blade.php ENDPATH**/ ?>