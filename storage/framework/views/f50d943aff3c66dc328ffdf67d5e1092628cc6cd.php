<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
    <title>Document</title>
    <style>

        th{

        }
    </style>
</head>
<body>

    <tr>
        <td>
            Shift
        </td>
    </tr>
    <table>
        <thead>
            <th  style='text-align:center;background-color: #000000 ;color:white ; font-weight:bold ;'>
                No
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                In
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                Out
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                Staff ID
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                Staff Name
            </th >
           
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
            <?php $__currentLoopData = $nshift; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $dt =>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                  <?php
                    $cnt = 3 + $dt;
                  ?>
                <tr>
                    <td style='text-align:center;'><?php echo e($dt + 1); ?></td>
                    <td><?php echo e($i->in); ?></td>
                    <td><?php echo e($i->out); ?></td>
                    <td><?php echo e($i->staff_systemid); ?></td>
                    <td><?php echo e($i->staff_name); ?></td>
                    
                    
                    
                   
                </tr>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        </tbody>

    </table>

    <table>
        <thead>
        <tr>
            <th><?php echo e(date('dMy',strtotime($start_date))); ?> - <?php echo e(date('dMy',strtotime($stop_date))); ?></th>
        </tr>
        <thead>
        <tr>
            <th>Fuel Receipt List</th>
        </tr>
        </thead>
        <thead>
        <tr>
            <th style='text-align:center; background-color: #000000 ;color:white ; font-weight:bold ;'>No</th>
            <th style='text-align:center; background-color: #000000 ;
              color:white ; font-weight:bold '>Pump No
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Date</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Staff ID</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Staff Name</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Receipt ID</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Price</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Total</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Rounding</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Fuel</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Filled</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Refund</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Void</th>

            <?php $__currentLoopData = $product_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th style="background-color: #000000 ;color:white ;
             font-weight:bold ; text-align:center;"><?php echo e($v->name); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            <?php $__currentLoopData = $product_name; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $v): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;"><?php echo e($v->name. ' Qty'); ?></th>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Cash</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Credit Card</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Wallet</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Credit Account</th>

        </tr>
        </thead>
        <tbody>
            <?php
                $newPrdList = App\Http\Controllers\ExcelExportController::getProductNamearray(
                    $product_name);
            ?>
            <?php $__currentLoopData = $receiptList; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in =>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $cnt = 8 + $in;
            ?>
            <tr>
                <td style='text-align:center'><?php echo e($in + 1); ?></td>
                <td style='text-align:center;'><?php echo e($i->pump_no); ?></td>
                <td><?php echo e(date('dMy H:i:s',strtotime($i->date))); ?></td>
                <td><?php echo e($i->staff_id); ?></td>
                <td><?php echo e($i->staff_name); ?></td>

                <td><?php echo e($i->receipt_i); ?></td>
                <td  style='text-align:right;' data-format="0.00"><?php echo e($i->price/100); ?></td>
                <td  style='text-align:right;' data-format="0.00" ><?php echo e($i->status=='voided'? 0: str_replace("'", "", $i->total/100)); ?></td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e($i->status =='refunded' ? $i->newsales_rounding/100:
                    $i->rounding/100); ?>

                </td>

                <td style='text-align:right;'data-format="0.00"><?php echo e($i->fuel /100); ?></td>
                <td  style='text-align:right;' data-format="0.00"><?php echo e($i->filled/100); ?></td>
                <td  style='text-align:right;' data-format="0.00"><?php echo e($i->refund/100); ?></td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e($i->status=='voided'?  $i->total/100:' '); ?>

                </td>

                <?php if(in_array($i->product_name,$newPrdList )): ?>
                    <?php
                        $length = count($product_name);
                        $position = array_search($i->product_name,$newPrdList );


                        if($i->status !='refunded'){
                            echo  str_replace("'", "", App\Http\Controllers\ExcelExportController::tdgenerator(
                                $position, $i->item_amount,$length,$i->status));

                        }elseif($i->status =='refunded'){
                            echo  str_replace("'", "", App\Http\Controllers\ExcelExportController::tdgenerator(
                                $position, $i->newsales_item_amount,$length,$i->status));
                        }
                        echo  str_replace("'", "", App\Http\Controllers\ExcelExportController::tdgenerator_qty($position, $i->quantity,$length,$i));
                    ?>
                <?php endif; ?>

                <td  style='text-align:right;' data-format="0.00">
                <?php echo e(str_replace("'", "", \App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod(
                    $i,$i->cash_received))); ?>

                </td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e(\App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod($i,$i->creditcard)); ?>

                </td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e(\App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod($i,$i->wallet)); ?>

                </td>
                <td  style='text-align:right;' data-format="0.00">
                    <?php echo e(\App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod($i,$i->creditac)); ?>

                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </tbody>
    </table>


</body>
</html>
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/excel_export/fuel_receiptlist_excel.blade.php ENDPATH**/ ?>