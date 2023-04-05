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
           {{--  <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                Cash
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                +Cash In
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                -Cash Out
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                -Sales Drop
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                Expected
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                Actual
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                Difference
            </th>
            <th style="background-color: #000000 ;color:white ; font-weight:bold ;">
                C-Store
            </th> --}}
        </thead>
        <tbody>
            <tr>
                <td></td>
            </tr>
            @foreach($nshift as $dt =>$i)
                  @php
                    $cnt = 3 + $dt;
                  @endphp
                <tr>
                    <td style='text-align:center;'>{{$dt + 1}}</td>
                    <td>{{$i->in}}</td>
                    <td>{{$i->out}}</td>
                    <td>{{$i->staff_systemid}}</td>
                    <td>{{$i->staff_name}}</td>
                    {{-- <td data-format="0.00">0.00</td>
                    <td data-format="0.00">{{ $i->cash_in/100}}</td>
                    <td data-format="0.00">{{ $i->cash_out/100}}</td>
                    <td data-format="0.00" style='text-align:right;'>{{$i->sales_drop /100}}</td>
                    <td data-format="0.00" style='text-align:right;'>{{trim("=T$cnt-U$cnt")}}</td>
                    <td data-format="0.00" style='text-align:right;'>{{ $i->actual/100}}</td>
                    <td data-format="0.00" style='text-align:right'>=K{{$cnt}}-J{{$cnt}}</td>
                    {{-- <td data-format="0.00">{{ $i->cstore/100}}</td> --}}
                    {{-- <td data-format="0.00" >0</td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td>
                    <td></td> --}}
                    {{-- this might affect the excexl sheet --}}
                   {{--  <td  style="display:none; color:white;">=F{{$cnt}}+G{{$cnt}}</td>
                    <td  style="display:none; color:white;">=H{{$cnt}}+I{{$cnt}}</td>
 --}}
                </tr>
            @endforeach
        </tbody>

    </table>

    <table>
        <thead>
        <tr>
            <th>{{ date('dMy',strtotime($start_date)) }} - {{ date('dMy',strtotime($stop_date)) }}</th>
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

            @foreach($product_name as $v)
            <th style="background-color: #000000 ;color:white ;
             font-weight:bold ; text-align:center;">{{ $v->name }}</th>
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
                $newPrdList = App\Http\Controllers\ExcelExportController::getProductNamearray(
                    $product_name);
            @endphp
            @foreach($receiptList as $in =>$i)
            @php
                $cnt = 8 + $in;
            @endphp
            <tr>
                <td style='text-align:center'>{{ $in + 1}}</td>
                <td style='text-align:center;'>{{ $i->pump_no }}</td>
                <td>{{ date('dMy H:i:s',strtotime($i->date)) }}</td>
                <td>{{ $i->staff_id }}</td>
                <td>{{ $i->staff_name }}</td>

                <td>{{ $i->receipt_i}}</td>
                <td  style='text-align:right;' data-format="0.00">{{ $i->price/100 }}</td>
                <td  style='text-align:right;' data-format="0.00" >{{ $i->status=='voided'? 0: str_replace("'", "", $i->total/100) }}</td>
                <td  style='text-align:right;' data-format="0.00">
                    {{ $i->status =='refunded' ? $i->newsales_rounding/100:
                    $i->rounding/100 }}
                </td>

                <td style='text-align:right;'data-format="0.00">{{ $i->fuel /100 }}</td>
                <td  style='text-align:right;' data-format="0.00">{{  $i->filled/100 }}</td>
                <td  style='text-align:right;' data-format="0.00">{{ $i->refund/100 }}</td>
                <td  style='text-align:right;' data-format="0.00">
                    {{  $i->status=='voided'?  $i->total/100:' ' }}
                </td>

                @if(in_array($i->product_name,$newPrdList ))
                    @php
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
                    @endphp
                @endif

                <td  style='text-align:right;' data-format="0.00">
                {{ str_replace("'", "", \App\Http\Controllers\ExcelExportController::FuelReceiptpaymentMethod(
                    $i,$i->cash_received))}}
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

        </tbody>
    </table>


</body>
</html>
