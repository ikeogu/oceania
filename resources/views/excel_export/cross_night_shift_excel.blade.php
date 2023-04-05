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
<tr>

    <table>
        <thead>
        <tr>
            <!-- <th>{{ date('dMy',strtotime($start_date)) }} - {{ date('dMy',strtotime($stop_date)) }}</th> -->
        </tr>
        <thead>
        <tr>
            <th>Fuel Receipt List</th>
        </tr>
        </thead>
        <thead>
        <tr>
            <th style="text-align:center;background-color: #000000 ;color:white ; font-weight:bold ;">No</th>
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
            @foreach($product_name as $v)
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">{{ $v->name }}</th>
            @endforeach
            @foreach($product_name as $v)
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">{{ $v->name. ' Qty' }}</th>
            @endforeach
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Cash</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Credit Card</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Wallet</th>
            <th  style="background-color: #000000 ;color:white ; font-weight:bold ;">Credit Account</th>

        </tr>
        </thead>
        <tbody>
            @php
                $newPrdList = App\Http\Controllers\ExcelExportController::getProductNamearray($product_name);
            @endphp
            @foreach($receiptList as $in =>$i)
            @php
                $cnt = 8 + $in;
            @endphp
            <tr>
                <td style='text-align:center;'>{{ $in + 1}}</td>
                <td style='text-align:center;'>{{ $i->pump_no }}</td>
                <td>{{ date('dMy H:i:s',strtotime($i->date)) }}</td>
                <td>{{ $i->staff_id }}</td>
                <td>{{ $i->staff_name }}</td>

                <td>{{ $i->receipt_i}}</td>
                <td  style='text-align:right;' data-format="0.00">{{ number_format($i->price/100,2) }}</td>
                <td  style='text-align:right;' data-format="0.00">{{$i->status=='voided'? number_format(0,2): number_format($i->total/100,2)}}</td>
                <td  style='text-align:right;' data-format="0.00">{{ $i->status =='refunded' ? number_format($i->newsales_rounding/100,2):number_format($i->rounding/100,2) }}</td>

                <td style='text-align:right;'data-format="0.00">{{number_format( $i->fuel /100,2) }}</td>
                <td  style='text-align:right;' data-format="0.00">{{  number_format($i->filled/100,2) }}</td>
                <td  style='text-align:right;' data-format="0.00">{{number_format( $i->refund/100,2) }}</td>
                <td  style='text-align:right;' data-format="0.00">{{  $i->status=='voided'? number_format( $i->total/100,2):' ' }}</td>
                @if(in_array($i->product_name,$newPrdList ))
                    @php
                        $length = count($product_name);
                        $position = array_search($i->product_name,$newPrdList );


                        if($i->status !='refunded'){

                        echo  App\Http\Controllers\ExcelExportController::tdgenerator($position, $i->item_amount,$length,$i->status);
                        }elseif($i->status =='refunded'){

                        echo  App\Http\Controllers\ExcelExportController::tdgenerator($position, $i->filled + $i->newsales_item_amount,$length,$i->status);
                        }
                        echo  App\Http\Controllers\ExcelExportController::tdgenerator_qty($position, $i->quantity,$length,$i);
                    @endphp
                @endif

                <td  style='text-align:right;' data-format="0.00">
                    {{ \App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod($i,$i->cash_received)}}
                </td>
                <td  style='text-align:right;' data-format="0.00">
                    {{ \App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod($i,$i->creditcard)}}
                </td>
                <td  style='text-align:right;' data-format="0.00">
                    {{ \App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod($i,$i->wallet)}}
                </td>
                <td  style='text-align:right;' data-format="0.00">
                    {{ \App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod($i,$i->creditac)}}
                </td>
            </tr>
        @endforeach
            <tr></tr>

            @php
                $frl = [1];
            @endphp
            @foreach($frl as $rl)
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @php
                        $tbl = 6;
                        $tlr = 0;
                        if(sizeof($nshift) > 0) {
                            $tbl += (1 + sizeof($nshift));
                            $tlr = $tbl + sizeof($receiptList);
                        } else {
                            $tbl += 1;
                            $tlr = $tbl + 1;
                        }
                        $ct = $tbl;
                        $cd = $tlr - 1;

                        $totpos = $tlr + (sizeof($receiptList) + 2);
                    @endphp

                    <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">=H{{$totpos}}</td>
                    <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">=I{{$totpos}}</td>
                    <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">=J{{$totpos}}</td>
                    <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">=K{{$totpos}}</td>
                    <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">=L{{$totpos}}</td>
                    <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">=M{{$totpos}}</td>

                    @php
                        $alpha_product_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial('N', sizeof($product_name));
                        $result = $alpha_product_pos->result;
                        $next = $alpha_product_pos->next;
                    @endphp
                    @foreach($result as $v)
                        <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">={{$v}}{{$totpos}}</td>
                    @endforeach

                    @php
                        $alpha_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial($next, sizeof($product_name));
                        $result = $alpha_pos->result;
                        $next = $alpha_pos->next;
                    @endphp
                    @foreach($result as $v)
                        <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">={{$v}}{{$totpos}}</td>
                    @endforeach

                    @php
                        $alpha_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial($next, 4);
                        $result = $alpha_pos->result;
                        $next = $alpha_pos->next;
                    @endphp
                    @foreach($result as $v)
                        <td style='color: #000000; font-weight: bold;  text-align:right;' data-format="0.00">={{$v}}{{$totpos}}</td>
                    @endforeach

                </tr>
            @endforeach
            <tr></tr>

            @foreach($receiptList as $in =>$i)
                @php
                    $tbl = 10;
                    $tlr = 0;
                    if(sizeof($nshift) > 0) {
                        $tbl += sizeof($nshift) + sizeof($receiptList);
                        $tlr = $tbl + sizeof($receiptList);
                    } else {
                        $tbl += 1;
                        $tlr = $tbl + 1;
                    }

                    $start = 7 + sizeof($nshift);
                    $cnt = $start + $in;
                    $tbl = $tbl + $in;
                @endphp
                <tr>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    @if($cnt == $start)
                        <td style='color:white; display: none;' data-format="0.00">=H{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=I{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=J{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=K{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=L{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=M{{$cnt}}</td>

                        @php
                            $alpha_product_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial('N', sizeof($product_name));
                            $result = $alpha_product_pos->result;
                            $next = $alpha_product_pos->next;
                        @endphp
                        @foreach($result as $v)
                            <td style='color:white; display: none;' data-format="0.00">={{$v}}{{$cnt}}</td>
                        @endforeach

                        @php
                            $alpha_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial($next, sizeof($product_name));
                            $result = $alpha_pos->result;
                            $next = $alpha_pos->next;
                        @endphp
                        @foreach($result as $v)
                            <td style='color:white; display: none;' data-format="0.00">={{$v}}{{$cnt}}</td>
                        @endforeach

                        @php
                            $alpha_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial($next, 4);
                            $result = $alpha_pos->result;
                            $next = $alpha_pos->next;
                        @endphp
                        @foreach($result as $v)
                            <td style='color:white; display: none;' data-format="0.00">={{$v}}{{$cnt}}</td>
                        @endforeach
                    @else
                        <td style='color:white; display: none;' data-format="0.00">=H{{$tbl-1}}+H{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=I{{$tbl-1}}+I{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=J{{$tbl-1}}+J{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=K{{$tbl-1}}+K{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=L{{$tbl-1}}+L{{$cnt}}</td>
                        <td style='color:white; display: none;' data-format="0.00">=M{{$tbl-1}}+M{{$cnt}}</td>

                        @php
                            $alpha_product_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial('N', sizeof($product_name));
                            $result = $alpha_product_pos->result;
                            $next = $alpha_product_pos->next;
                        @endphp
                        @foreach($result as $v)
                            <td style='color:white; display: none;' data-format="0.00">={{$v}}{{$tbl-1}}+{{$v}}{{$cnt}}</td>
                        @endforeach

                        @php
                            $alpha_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial($next, sizeof($product_name));
                            $result = $alpha_pos->result;
                            $next = $alpha_pos->next;
                        @endphp
                        @foreach($result as $v)
                            <td style='color:white; display: none;' data-format="0.00">={{$v}}{{$tbl-1}}+{{$v}}{{$cnt}}</td>
                        @endforeach

                        @php
                            $alpha_pos = App\Http\Controllers\ExcelExportController::generateColumnAZserial($next, 4);
                            $result = $alpha_pos->result;
                            $next = $alpha_pos->next;
                        @endphp
                        @foreach($result as $v)
                            <td style='color:white; display: none;' data-format="0.00">={{$v}}{{$tbl-1}}+{{$v}}{{$cnt}}</td>
                        @endforeach
                    @endif
                </tr>
            @endforeach
        </tbody>
    </table>


</body>
</html>
