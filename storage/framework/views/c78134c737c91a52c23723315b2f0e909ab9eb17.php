<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
</head>
<body>

    <table>

        <thead>
        <tr>
            <th>Audited Report</th>
        </tr>
        <tr>
            <td>Document No</td>
            <td><?php echo e($docId); ?></td>
        <tr>

        </thead>
        <thead>
        <tr>
            <th style="text-align:center;background-color: #000000 ;color:white ; font-weight:bold ;">No</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Product ID</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Barcode</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Product Name</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Qty</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Audited Qty</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Difference</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Stock In</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Stock Out</th>
        </tr>
        </thead>
        <tbody>

            <?php $__currentLoopData = $auditedReport; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in =>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $cnt = 2 + $in;
            ?>
            <tr>
                <td style='text-align:center;'><?php echo e($in + 1); ?></td>
                <td style='text-align:center;'><?php echo e($i->psystemid); ?></td>
                <td style='text-align:center;'><?php echo e($i->barcode); ?></td>
                <td><?php echo e($i->name); ?></td>
               <td class="text-center">
                    <?php echo e(number_format($i->qty)); ?>

                </td>
                <td class="text-center">
                    <?php echo e(number_format($i->audited_qty)); ?>

                </td>
                 <td class="text-center">
                    <?php echo e(number_format($i->audited_qty - $i->qty)); ?>

                </td>

                <td class="text-center"
                    style="">
                    <?php if(($i->audited_qty - $i->qty) >=0): ?>
                            <?php echo e(number_format($i->audited_qty - $i->qty)); ?>

                    <?php else: ?>
                        0
                    <?php endif; ?>
                </td>
                <td class="text-center"
                    style="">
                    <?php if(($i->audited_qty - $i->qty) <=0): ?>
                        <?php echo e(number_format($i->audited_qty - $i->qty)); ?>

                    <?php else: ?>
                        0
                    <?php endif; ?>
                </td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </tbody>
    </table>

</body>
</html>
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/excel_export/audited_report_excel.blade.php ENDPATH**/ ?>