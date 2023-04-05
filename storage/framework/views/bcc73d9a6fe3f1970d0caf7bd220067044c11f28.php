<tr>
    <td>Stock Ledger</td>
     <td><?php echo e(date('dMy',strtotime($start_date)) .'-'.  date('dMy',strtotime($stop_date))); ?></td>
</tr>

<table>
  <?php $__currentLoopData = $products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $product): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
    <tr>
        <td style="font-weight: bold;"><?php echo e($product->name); ?></td>
    </tr>
    <thead>
        <tr >
            <th style="background-color: #000000; color:white; font-weight:bold;">No</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Document No</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Type</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Last Update</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Cost</th>
            <th style="background-color: #000000; color:white; font-weight:bold;">Qty</th>

        </tr>
    </thead>

    <tbody>
	<?php
		$cnt =0;
		$stock_ledgers = $stock_ledgers->sortBy('last_update');
		$stock_ledgers = $stock_ledgers->reverse();
	?>

	<?php $__currentLoopData = $stock_ledgers; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $stock_ledger): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

		<?php if($stock_ledger->product_systemid == $product->systemid): ?>
			<?php
			$cnt++;
			?>
			<tr>
				<td style="align:left; color:#000000;"><?php echo e($cnt); ?></td>
				<td><?php echo e($stock_ledger->systemid); ?></td>
				<td>
					<?php if($stock_ledger->type == 'stockin'): ?>
						Stock In
					<?php elseif($stock_ledger->type == 'stockout'): ?>
						Stock Out
					<?php elseif($stock_ledger->type == 'received'): ?>
						Received
                    <?php elseif($stock_ledger->type == 'returned'): ?>
						Returned
					<?php elseif($stock_ledger->type == 'cash_sales'): ?>
						Cash sales
					<?php endif; ?>
				</td>
				<td><?php echo e(date('dMy H:i:s',strtotime($stock_ledger->last_update))); ?></td>
				<td data-format="0.00"> <?php echo e($stock_ledger->cost); ?></td>
				<td><?php echo e($stock_ledger->qty); ?></td>
			</tr>
		<?php endif; ?>
	<?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

    </tbody>
    <tr></tr>
    <tr></tr>
    <tr></tr>
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

</table>
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/excel_export/stock_ledger_excel.blade.php ENDPATH**/ ?>