<!DOCTYPE html>
<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <!-- CSRF Token -->
    <meta name="csrf-token" content="<?php echo e(csrf_token()); ?>">

    <title><?php echo e(config('app.name', 'OPOSsum')); ?></title>

    <style>
        body {
            margin: 0;
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
            color: #212529;
            text-align: left;
            background-color: #fff;
        }

        .bg-refund {
            color: #fff;
            background: #ff7e30;
            border-color: #ff7e30
        }

        .table {
            width: 100% !important;
            border-style: none;
        }

        .thead-dark {
            color: white;
            border-color: #343a40;
            background-color: #343a40;

        }

        #table-th th {
            font-size: 12px !important;
        }

        .text-center {
            text-align: center;
        }

        .text-left {
            text-align: left;
        }

        .text-right {
            text-align: right;
        }

        .table-td td {
            font-size: 12px !important;
        }

        p {
            margin-top: 0;
            margin-bottom: 0rem;
            font-size: 12px;
        }

        hr {
            margin-bottom: 0;
        }

        #item tr td {
            padding-top: 6px !important;
            padding-bottom: 6px !important;
            vertical-align: middle !important;
        }

        #item tr th {
            font-size: 12px;
            padding-top: 8px !important;
            padding-bottom: 8px !important;
            vertical-align: middle !important;
        }

        td {
            border-style: none;
        }

        th {
            border-style: none;
        }

        .text-bold {
            font-weight: bold;
            font-size: 12px;
        }

        span {
            font-size: 12px;
        }

        tr td span {
            /*width: 80px !important;*/
            /*height: 60px !important;*/
            text-align: center;
            vertical-align: middle;
            font-size: 12px;
            cursor: pointer;
            padding: 10px 20px;
            color: black;
            display: inline-block;
            font-weight: 400;
            margin-top: 10px;
        }

        .active {
            border-radius: 10px;
            color: white;
            padding: 10px 25px;
            background-color: black;
        }

        .rad-info-box .heading {
            font-size: 1.2em;
            font-weight: 300;
            text-transform: uppercase;
        }
    </style>
</head>


<body>
    <table border="" style="width:100%; border-collapse: collapse" cellspacing="0" cellpadding="0">
        <tr>
            <td valign="center" rowspan="2" colspan="2">
                <b style="font-size:30px;font-weight:700;word-wrap:normal;margin-left:0;">

                   Shift
                </b>
            </td>

        </tr> <br><br>
         <tr>
            <td valign="bottom"  rowspan="2" colspan="9" align="left">
				<!--
                <p style="font-weight: 700;font-size: 12px">
                    Shift No:<?php echo e($shift->shift_no); ?>

                </p>
				-->
                <p style="font-weight: 700;font-size: 14px">
                    <?php echo e($shift->staff_name); ?>

                </p>
                 <p style="font-weight: 700;font-size: 13px">
                    <?php echo e($shift->staff_id_systemid); ?>

                </p>
                 <p style="font-weight: 700;font-size: 12px">
                   <?php echo e(date('dMy H:i:s',strtotime($shift->shift_in))); ?> - <?php echo e(date('dMy H:i:s',strtotime($shift->shift_out))); ?>

                </p>
            </td>
        </tr>
    </table>

    <table border="0" cellpadding="0" cellspacing="1" class="table mt-4" id="item" style="width:100%">
        <thead class="thead-dark " style="">
            <tr id="table-th" style="border-style: none;">
                <th valign="middle" class="text-center" style="width:5%;">No</th>
                <th valign="middle" class="text-left" style="width:55%;">Product</th>
                <th valign="middle" class="text-center" style="width:20%;">Qty</th>
                <th valign="middle" class="text-center" style="width:20%">Sales</th>
            </tr>
        </thead>

        <?php
        $grandTotal_sales = 0;
        $grandTotal_qty = 0;
        $num = 0;
        ?>
        <?php $__currentLoopData = $receipt_sld; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $key => $rep): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>

        <tr class="table-td">
            <td class="text-center" style="border-style: none">
                <?php echo e($key + 1); ?>

            </td>
            <td class="text-left" style="padding-left:10px;border-style: none">
                <?php echo e($rep->product_name); ?>

            </td>
            <td align="center" style="border-style: none;">
                <?php echo e(number_format($rep->qty/100,2)); ?>

            </td>
            <td style="text-align:right;border-style: none;padding-right:10px">
                <?php echo e(number_format($rep->sales /100,2)); ?>

            </td>

            <?php
				$grandTotal_qty += $rep->qty / 100;
				$grandTotal_sales += $rep->sales;
            ?>
        </tr>
        <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
        <tr>
           <td class="text-center" style="border-style: none">
                <?php echo e(count($receipt_sld) + 1); ?>

            </td>
            <td class="text-left" style="padding-left:10px;border-style: none">
                Cstore
            </td>
            <td align="center" style="border-style: none;">

            </td>
            <td style="text-align:right;border-style: none;padding-right:10px">
                <?php echo e(number_format($cstore /100,2)); ?>

            </td>

            <?php
				// $grandTotal_qty += $rep->qty / 100;
				$grandTotal_sales +=  $cstore;
            ?>
        </tr>
        <tr>
            <td colspan="4" valign="middle">
                <div style="border-top: 1px solid #a0a0a0;"></div>
            </td>
        </tr>
        <tr>
            <td colspan="2" valign="baseline">
                <p style="text-decoration-line: none;font-size: 15px;font-weight: 700;padding: 0;margin-left:15px;margin-top: -5px; ">
                    Total
                </p>
            </td>
            <td align="center" valign="baseline">
                <p style="text-decoration-line: none;font-size: 15px;font-weight: 700;">
                    <?php echo e(number_format($grandTotal_qty,2)); ?>

                </p>
            </td>
            <td align="right" valign="baseline" colspan="3">
                <p style="text-decoration-line: none;font-size: 15px;font-weight: 700;padding-right:10px">
                    <?php echo e(number_format($grandTotal_sales/100,2)); ?>

                </p>
            </td>
        </tr>
    </table>
</body>

</html>
<?php /**PATH /home/emmanuel/oceania/trunk/oceania/resources/views/nshift/nshift_fuelsales_summary_pdf.blade.php ENDPATH**/ ?>