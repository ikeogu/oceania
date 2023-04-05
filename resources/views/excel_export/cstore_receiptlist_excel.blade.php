
<table>
     <thead>
     <tr>
        <th>{{ date('dMy',strtotime($start_date)) .'-'.  date('dMy',strtotime($stop_date)) }}</th>
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
         @foreach($product_name as $v)
        <th >{{ $v->name }}</th>
        @endforeach

        <th>Cash</th>
        <th>Credit Card</th>
        <th>Wallet</th>
    </tr>
    </thead>
    <tbody>
        @php
         $newPrdList = App\Http\Controllers\ExcelExportController::getProductNamearray($product_name);
        @endphp
        @foreach($cstore_products as $i)

            <tr>
                <td >{{ $i->id}}</td>
                <td>{{ date('dMy H:i:s',strtotime($i->date)) }}</td>
                <td>{{ $i->staff_id }}</td>
                 <td>{{ $i->staff_name }}</td>
                <td>{{ $i->receipt_i}}</td>
                <td  style='text-align:right;' data-format="0.00">
                    {{ ($i->status=='voided') ? number_format(0,2):
                        number_format($i->total/100,2) }}
                </td>
                <td  style='text-align:right;' data-format="0.00">
                    {{ ($i->status=='voided') ? number_format(0,2):
                    number_format($i->rounding/100,2) }}
                </td>
                <td  style='text-align:right;' data-format="0.00">{{number_format( $i->refund,2) }}</td>
                <td  style='text-align:right;' data-format="0.00">
                    {{  $i->status=='voided'? number_format( $i->total/100,2):' ' }}
                </td>
                @if(!is_array($i->product_name) && in_array($i->product_name,$newPrdList ) && !is_array($i->price))
                    @php
                        $length = count($product_name);
                        $position = array_search($i->product_name,$newPrdList );

                           echo App\Http\Controllers\ExcelExportController::
                            tdgenerator($position, $i->item_amount,$length,$i->status);
                    @endphp
                @endif
                @if(is_array($i->product_name) && is_array($i->price))
                    @php
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

                    @endphp
                @endif

                 <td  style='text-align:right;' data-format="0.00">
                    {{ \App\Http\Controllers\ExcelExportController::
                        CstorepaymentMethod($i,$i->cash_received)}}
                </td>
                <td  style='text-align:right;' data-format="0.00">
                    {{ \App\Http\Controllers\ExcelExportController::
                        CstorepaymentMethod($i,$i->creditcard)}}
                </td>
                <td  style='text-align:right;' data-format="0.00">
                    {{ \App\Http\Controllers\ExcelExportController::
                        CstorepaymentMethod($i,$i->wallet)}}
                </td>


            </tr>
        @endforeach
    </tbody>
</table>
