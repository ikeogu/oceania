<?php $__currentLoopData = $product_data; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $p): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
<div id="show_hide_single_product<?php echo e($p->id); ?>"
	class="row col-md-12 text-white py-2 align-items-center"
	style="
	padding-top:0 !important;
	padding-bottom:0 !important;
	font-size:20px;
	cursor:pointer;
	margin-left: 80px;
	"
	onclick="add_product('<?php echo e($p->id); ?>',
		'<?php echo e($p->systemid); ?>',
		'<?php echo e($p->thumbnail_1); ?>',
		'<?php echo e($p->name); ?>',
		1,
		'<?php echo e(number_format($p->recommended_price/100,2)); ?>')">

	<div class="col-1 index_div" style="margin-left: -115px;" >
		<?php echo e($loop->index + 1); ?>

	</div>

	<div class="col-9 mt-1 mb-1 pr-0">
		<img src='/images/product/<?php echo e($p->systemid); ?>/thumb/<?php echo e($p->thumbnail_1); ?>' 
			data-field='inven_pro_name'
			style=' width: 30px; height: 30px;
			display: inline-block;margin-right:5px;
			background-color:white;border-radius:5px;
			object-fit:contain;'>
		<span style=""><?php echo e($p->name); ?></span>
	</div>

	<div style="padding-right:0px;margin-left: 80px;"
		class="col-2 text-right pl-0">
		<?php echo e(number_format($p->recommended_price/100,2)); ?>

	</div>
</div>
<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/cstore/screen_a_products.blade.php ENDPATH**/ ?>