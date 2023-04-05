<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <meta http-equiv=\"Content-Type\" content=\"text/html;charset=utf-8\">
    <style>

        th{

        }
    </style>
</head>
<body>

    <table>

        <thead>
        <tr>
            <th>Returning Note</th>
        </tr>
        </thead>
        <thead>
        <tr>
            <th style="text-align:center;background-color: #000000 ;color:white ; font-weight:bold ;">No</th>

            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Barcode</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Product Name</th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">Qty</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Cost</th>

        </tr>
        </thead>
        <tbody>

            <?php $__currentLoopData = $returningNote; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $in =>$i): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
            <?php
                $cnt = 2 + $in;
            ?>
            <tr>
                <td style='text-align:center;'><?php echo e($in + 1); ?></td>
                <td style='text-align:center;'><?php echo e($i->barcode); ?></td>
                <td><?php echo e($i->name); ?></td>
                <td><?php echo e($i->qty); ?></td>
                <td  style='text-align:right;' data-format="0.00"><?php echo e($i->cost/100); ?></td>
            </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>

        </tbody>
    </table>


</body>
</html>
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/excel_export/returning_note_excel.blade.php ENDPATH**/ ?>