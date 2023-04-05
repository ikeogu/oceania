
<table>
     <thead>
     <tr>
        <th><?php echo e(date('dMy',strtotime($start_date)) .'-'.  date('dMy',strtotime($stop_date))); ?></th>
    </tr>
    </thead>
    <thead>
    <tr>
        <th> C-Store Receipt List.</th>
    </tr>
    </thead>
    <thead>
    <tr>
        <th>No</th>
        <th>Date</th>
        <th>Staff ID</th>
        <th>Staff Name</th>
        <th>Receipt ID</th>
        <th>Total</th>
        <th>Rounding</th>
        <th>Refund</th>
         <th>Void</th>
         <?php $__currentLoopData = $product_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
        <th ><?php echo e($v->name); ?></th>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        <th>Cash</th>
        <th>Credit Card</th>
        <th>Wallet</th>
    </tr>
    </thead>
    <tbody>
        <?php
         $newPrdList = App\Http\Controllers\ExcelExportController::getProductNamearray($product_name);
        ?>
        <?php $__currentLoopData = $cstore_products; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

            <tr>
                <td ><?php echo e($i->id); ?></td>
                <td><?php echo e(date('dMy H:i:s',strtotime($i->date))); ?></td>
                <td><?php echo e($i->staff_id); ?></td>
                 <td><?php echo e($i->staff_name); ?></td>
                <td><?php echo e($i->receipt_i); ?></td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e(($i->status=='voided') ? number_format(0,2):
                        number_format($i->total/100,2)); ?>

                </td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e(($i->status=='voided') ? number_format(0,2):
                    number_format($i->rounding/100,2)); ?>

                </td>
                <td  style='text-align:right;' data-format="0.00"><?php echo e(number_format( $i->refund,2)); ?></td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e($i->status=='voided'? number_format( $i->total/100,2):' '); ?>

                </td>
                <?php if(!is_array($i->product_name) && in_array($i->product_name,$newPrdList ) && !is_array($i->price)): ?>
                    <?php
                        $length = count($product_name);
                        $position = array_search($i->product_name,$newPrdList );

                           echo App\Http\Controllers\ExcelExportController::
                            tdgenerator($position, $i->item_amount,$length,$i->status);
                    ?>
                <?php endif; ?>
                <?php if(is_array($i->product_name) && is_array($i->price)): ?>
                    <?php
                    $length = count($product_name);
                        $position = array();

                        foreach ($i->product_name as $value) {
                            # code...
                            array_push($position,array_search($value,$newPrdList) );
                        }
                        // dd($position);
                        $allItems = array();
                        $price = array_values($i->price);
                        $allItems = array_combine($position,$price);

                       echo App\Http\Controllers\ExcelExportController::
                        cstore_Ntd_generator($allItems,$i->status,$length);

                    ?>
                <?php endif; ?>

                 <td  style='text-align:right;' data-format="0.00">
                    <?php echo e(\App\Http\Controllers\ExcelExportController::
                        CstorepaymentMethod($i,$i->cash_received)); ?>

                </td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e(\App\Http\Controllers\ExcelExportController::
                        CstorepaymentMethod($i,$i->creditcard)); ?>

                </td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e(\App\Http\Controllers\ExcelExportController::
                        CstorepaymentMethod($i,$i->wallet)); ?>

                </td>


            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
    </tbody>
</table>
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/excel_export/cstore_receiptlist_excel.blade.php ENDPATH**/ ?>