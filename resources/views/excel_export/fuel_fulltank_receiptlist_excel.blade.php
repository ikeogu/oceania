
<table>
    <thead>
     <tr>
        <th>{{ date('dMy',strtotime($start_date)) }} - {{ date('dMy',strtotime($stop_date)) }}</th>

    </tr>
    <thead>
    <tr>
        <th> Full tank Receipt List</th>

    </tr>
    </thead>
    <thead>
    <tr>
        <th>No</th>
        <th style='text-align:center;'>Pump No</th>
        <th>Date</th>

        <th>Receipt ID</th>
         <th>Price</th>
        <th>Total</th>
          <th >Rounding</th>
         @foreach($product_name as $v)
        <th >{{ $v->name }}</th>
        @endforeach
         @foreach($product_name as $v)
        <th >{{ $v->name .' Qty' }}</th>
        @endforeach

        <th>Cash</th>
        <th>Credit Card</th>
        <th>Wallet</th>
        <th>Credit Account</th>

    </tr>
    </thead>
     @php
        $newPrdList = App\Http\Controllers\ExcelExportController::getProductNamearray($product_name);
     @endphp
    <tbody>
    @foreach($receiptList as $in =>$i)
        <tr>
            <td>{{$in + 1}}</td>
            <td style='text-align:center;'>{{ $i->pump_no }}</td>
            <td>{{ date('dMy H:i:s',strtotime($i->Fcreated_at)) }}</td>
            <td>{{ $i->systemid }}</td>
             <td  style='text-align:right;' data-format="0.00">{{ number_format($i->price/100,2) }}</td>
            <td  style='text-align:right;' data-format="0.00">{{ $i->status=='voided'? number_format(0,2) : number_format($i->Total/100,2) }}</td>
             <td  style='text-align:right;' data-format="0.00">{{ number_format($i->rounding/100,2) }}</td>

            @if(in_array($i->product_name,$newPrdList ))
                @php
                    $length = count($product_name);
                    $position = array_search($i->product_name,$newPrdList );
                    echo App\Http\Controllers\ExcelExportController::fuel_fulltank_tdgenerator($position, $i->Total + $i->rounding,$length,$i->status);
                     echo App\Http\Controllers\ExcelExportController::tdgenerator_qty($position, $i->quantity,$length,$i);
                @endphp
            @endif

            <td  style='text-align:right;' data-format="0.00">
                {{ \App\Http\Controllers\ExcelExportController::FulltankReceiptpaymentMethod($i,$i->cash_received)}}
            </td>
            <td  style='text-align:right;' data-format="0.00">
                {{ \App\Http\Controllers\ExcelExportController::FulltankReceiptpaymentMethod($i,$i->creditcard)}}
            </td>
            <td  style='text-align:right;' data-format="0.00">
                 {{ \App\Http\Controllers\ExcelExportController::FulltankReceiptpaymentMethod($i,$i->wallet)}}
            </td>
            <td  style='text-align:right;' data-format="0.00">
                {{ \App\Http\Controllers\ExcelExportController::FulltankReceiptpaymentMethod($i,$i->creditac)}}
            </td>
        </tr>
    @endforeach
    </tbody>
</table>

