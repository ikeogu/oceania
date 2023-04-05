<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<!-- CSRF Token -->
<meta name="csrf-token" content="{{ csrf_token() }}">

<title>{{ config('app.name', 'OPOSsum') }}</title>

<style>
    @page { margin: 10px; }

body {
	margin: 10px;
	font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, "Noto Sans", sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol", "Noto Color Emoji";
	color: #212529;
	text-align: left;
	background-color: #fff;

}
.bg-refund{
	color:#fff;
	background:#ff7e30;
	border-color:#ff7e30
}
.table{
	width: 100%!important;
	border-style: none;
}
.thead-dark {
	color: white;
	border-color: #343a40;
	background-color: #343a40;

}
.br{
    margin-bottom:5px !important;
}
#table-th th{
	font-size: 12px!important;
}
.text-center{
	text-align: center;
}
.text-left{
	text-align: left;
}
.text-right{
	text-align: right;
}
.table-td td{
	font-size: 12px!important;
}
p {
	margin-top: 0;
	margin-bottom: 0rem;
	font-size: 12px;
}

.solid {
    border: 0;
    border-top: 0.5px solid #c0c0c0;
    border-style: solid;
    width: 92% !important;
    position: fixed;
  }

  .line {    
    border: 0.5px solid #c0c0c0;
    border-style: solid;
    width:92% !important;
    position: fixed;
  }

#item tr  td {
	padding-top: 0px !important;
	padding-bottom: 0px !important;
	vertical-align: middle !important;
}
#item tr th{
	font-size: 12px;
	padding-top: 8px !important;
	padding-bottom: 8px !important;
	vertical-align: middle !important;
}

td{
	border-style: none;
    padding:0px;
    margin:0px;
}
th{
	border-style: none;
}


.text-bold {
	font-weight: bold;
	font-size: 12px;
}

span {
	font-size: 12px;
}

tr td span{
	/*width: 80px !important;*/
	/*height: 60px !important;*/
	/*text-align: center;*/
	vertical-align: middle;
	font-size: 12px;
	cursor: pointer;
	/*padding: 10px 20px;*/
	color: black;
	display: inline-block;
	font-weight: 400;
	margin-top: 0px;
}
.active{
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

<style>
    .receipt-item-l {
        text-align: left;
        padding-right: 0;
        font-size: 12px;
    }

    .receipt-item-c {
        padding-right: 0;
        padding-left: 0;
        font-size: 12px;
    }

    .receipt-item-discount {
        text-align: center;
        padding-right: 0;
        padding-left: 20px;
        font-size: 12px;
    }

    .receipt-item-r {
        text-align: right;
        padding-left: 0;
        font-size: 12px;
    }

    .void-stamp {
        font-size: 100px;
        color: red;
        position: absolute;
        z-index: 2;
        font-weight: 500;
        /* margin-top:50%; */
        margin-left: 10%;
        transform: rotate(45deg);
        font-family: sans-serif !important;
        font-style: normal;
        font-weight: normal;
    }

</style>
</head>
<body>


<div class="rec_id" style="" id="{{ $receipt->id }}">
    <!--Section 1 starts-->

    <div class="row" style="text-align:center;">
        <div class="col-md-12 text-center mt-4">
            @if (!empty($company->id) && !empty($receipt->receipt_logo))
            <img src="{{ asset('images/company/' . $company->id . '/corporate_logo/' . $receipt->receipt_logo) }}"
            alt="" style="object-fit:contain;width: 80px !important;" srcset="">
            @endif
        </div>
    </div>
    {{ Log::debug('Logo'.asset('images/company/' . $company->id . '/corporate_logo/' . $receipt->receipt_logo))}}
    <div class="row" style="text-align:center;">
        <div class="col-md-12 text-center pl-5 pr-5" style="font-size: 17px">
            <b>
                {{ !empty($receipt->company_name) ? $receipt->company_name : '' }}
            </b><br>
            <span style="font-size:12px; font-weight:normal">
                ({{ !empty($receipt->business_reg_no) ? $receipt->business_reg_no : '' }})
            </span><br>
            <span style="font-size:12px; font-weight:normal">
                {{ !empty($receipt->gst_vat_sst) ? '(SST No. ' . $receipt->gst_vat_sst . ')' : '' }}
            </span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 text-center">
            <span style="font-size: 14px; font-weight:normal">
                {{ !empty($receipt->receipt_address) ? $receipt->receipt_address : '' }}
            </span>
        </div>
    </div>
    <hr style="border:0px !important;padding:2px !important;margin:0px !important;">
   <hr class="solid" style="margin: 0px !important;padding: 0px !important;"><hr style="border:0px !important;padding:0px !important;margin:0px !important;">

    
  <table border="0" cellpadding="0" cellspacing="0" class="table" id="item" style="width:100%;padding:0px !important;margin:0px !important;">
    <thead class="">
    <tr id="table-th" style="border-style: none;font-size:12px;">
        <th valign="middle" class="text-left" width="40%">Description</th>
        <th valign="middle" class="text-center" width="15%">Qty</th>
        <th valign="middle" class="text-right" width="15%">Price</th>
        <th valign="middle" class="text-center" width="15%">Disc.</th>
        <th valign="middle" class="text-right" width="15%">{{ !empty($receipt->currency) ? $receipt->currency : 'MYR' }}</th>
    </tr>

    </thead>

    <hr style="border:0px !important;padding:0px !important;margin:0px !important;">
   <hr class="solid" style="margin: 0px !important;padding: 0px !important;"><hr style="border:0px !important;padding:3px !important;margin:0px !important;">

    <tbody>
    @if (!empty($receiptproduct))
    @foreach ($receiptproduct as $product)

                <tr class="table-td" style="margin-bottom:10px !important;">
                    <td style="text-align:left;border-style: none">
                        <span>{{!empty($product->name)?$product->name:"RON95"}}</span>
                    </td>
                    <td style="text-align:center;border-style: none">
                        <span> {{!empty($product->quantity)?number_format($product->quantity):"1"}}</span>
                    </td>
                    <td style="text-align:right;border-style: none">
                        <span> {{!empty($product->price)?number_format($product->price/100,2):"0.00"}}</span>
                    </td>
                    <td style="text-align:center;border-style: none">
                        <span> {{!empty($product->discount)?$product->discount_pct:"0"}}%</span>
                    </td>

                    <td style="text-align:right;border-style: none">
                        <span id="item_amount">
                        {{number_format(($product->quantity*($product->price/100)) - ($product->discount / 100),2)}}
                         {{--number_format((($receipt->cash_received/100-$receipt->cash_change/100)??"2"),2)--}}
                        </span>
                    </td>

                </tr>

        @endforeach
    @endif
            </tbody>
  </table>
  <hr style="border:0px !important;padding:3px !important;margin:0px !important;">
   <hr class="solid" style="margin: 0px !important;padding: 0px !important;"><hr style="border:0px !important;padding:3px !important;margin:0px !important;">

    <table border="0" cellpadding="0" cellspacing="0" class="table" id="item" style="width:100%;padding:0px !important;margin:0px !important;">
    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Item Amount</span>
        </td>
        @php
			if (!empty($receiptdetails)) {
				$item_amount = $receiptdetails->item_amount;
			} else {
				$item_amount = 0;
			}
		@endphp
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight:normal" id="item_amount">
            {{number_format($item_amount/ 100, 2)}}
            {{--number_format((($receipt->cash_received/100-$receipt->cash_change/100)/(1+($terminal->tax_percent/100))),2)--}}
            </span>
        </td>
    </tr>

    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">{{!empty($terminal->taxtype)?strtoupper($terminal->taxtype):"SST"}} {{(float) $receipt->service_tax??"6"}}%</span>
        </td>
        <td style="text-align: right;font-weight: normal;">
            <span id="item_amount" style="font-weight: normal">
            @if(!empty($receiptdetails))
                    {{number_format($receiptdetails->sst/100,2)}}
                @else
                    {{number_format(0,2)}}
                @endif
            </span>
        </td>
    </tr>

    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Rounding</span>
        </td>
        <td style="text-align: right;font-weight: normal;">
            <span id="rounding_item_amount" style="font-weight: normal">
            @if(!empty($receiptdetails))
                    {{number_format($receiptdetails->rounding / 100, 2)}}
                @else
                    {{number_format(0,2)}}
                @endif</span>
        </td>
    </tr>
    <div class="void-stamp" id="void-stamp{{ $receipt->id ?? '' }}" style="margin-left:0% ;font-family: sans-serif !important;">
             VOID
         </div>
    <!--section 1 ends--><hr style="border:0px !important;padding:3px !important;margin:0px !important;">
   <hr class="solid" style="margin: 0px !important;padding: 0px !important;"><hr style="border:0px !important;padding:3px !important;margin:0px !important;">

    <!--section 2 starts-->
    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal"><strong>Total</strong></span>
        </td>
        <td style="text-align: right;font-weight: normal;">
            <span style="font-weight:bold" id="total_amount_unq">
                @if(!empty($receiptdetails))
                    {{ number_format( ($receiptdetails->item_amount / 100) + ($receiptdetails->sst / 100) + ($receiptdetails->rounding /100) , 2)}}
                @else
                    {{number_format(0,2)}}
                @endif
            </span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Cash Received</span>
        </td>
        <td style="text-align: right;font-weight: normal;">
            <span style="font-weight:normal" id="item_amount">
            @if ($receipt->payment_type == "cash")
					{{!empty($receipt->cash_received)?number_format(($receipt->cash_received/100),2):"0"}}
                @else
                    {{number_format(0,2)}}
                @endif

            </span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Credit Card</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight:normal" id="item_amount">
            @if ($receipt->payment_type == "creditcard")
					{{!empty($receipt->cash_received)?number_format((($receipt->cash_received/100)+((5 * round(($receipt->cash_received-$receipt->cash_change) / 5))-($receipt->cash_received-$receipt->cash_change))/100),2):"0"}}
                @else
                    {{number_format(0,2)}}
                @endif
            </span>
        </td>
    </tr>

    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Wallet</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight:normal" id="item_amount">
            @if ($receipt->payment_type == "wallet")
					{{!empty($receipt->cash_received)?number_format((($receipt->cash_received/100)+((5 * round(($receipt->cash_received-$receipt->cash_change) / 5))-($receipt->cash_received-$receipt->cash_change))/100),2):"0"}}
                @else
                    {{number_format(0,2)}}
                @endif
            </span>
        </td>
    </tr>     


    <!--section 2 ends-->
    <hr style="border:0px !important;padding:2px !important;margin:0px !important;">
   <hr class="solid" style="margin: 0px !important;padding: 0px !important;"><hr style="border:0px !important;padding:3px !important;margin:0px !important;">


    <!--section 3 starts-->
    <tr style="font-weight: normal;">
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Change</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight: normal"> {{!empty($receipt->cash_change)?number_format((($receipt->cash_change/100)-((5 * round(($receipt->cash_received-$receipt->cash_change) / 5))-($receipt->cash_received-$receipt->cash_change))/100),2):"0.00"}}</span>
        </td>
        </tr>
        <hr style="border:0px !important;padding:2px !important;margin:0px !important;">
   <hr class="solid" style="margin: 0px !important;padding: 0px !important;"><hr style="border:0px !important;padding:3px !important;margin:0px !important;">

    <!--section 3 ends-->

    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Receipt No.</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span
                style="font-weight: normal">{{!empty($receipt->systemid)?$receipt->systemid:""}}</span>
        </td>
    </td>
    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Location</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight: normal"> {{$location->name??""}}</span>
        </td>
    </td>
    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Terminal ID</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight: normal">  {{$terminal->systemid??''}}</span>
        </td>
    </tr>

    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Staff Name</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight: normal">{{ $user->fullname ?? '' }}</span>
        </td>
    </tr>
    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Staff ID</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight: normal"> {{ $user->systemid ?? '' }}</span>
        </td>
    </tr>

    <tr>
        <td style="text-align: left;font-weight: normal">
            <span style="font-weight: normal">Date</span>
        </td>
        <td style="text-align: right;font-weight: normal">
            <span style="font-weight: normal">
                {{ date('dMy H:i:s', strtotime($receipt->created_at ?? '')) }}
            </span>
        </td>
    </tr>
    

</table>

</div>

<div class="row d-flex" style="justify-content:center">
    <div style="font-size:14px" class="text-center">
        Thank You!
    </div>

</div>
<!--- void by --->

<div id="void-div{{ $receipt->id ?? '' }}" style="" >
        <div class="row">
            <div class="col-md-3 text-left" style="color:red;font-size:12px;font-weight:normal;">
                <strong>Void By</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left" style="color:red;font-size:12px;font-weight:normal;">
                <strong> {{ $user->fullname ?? '' }}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left" style="color:red;font-size:12px;font-weight:normal;">
                <strong> {{ $user->systemid ?? '' }}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left" style="color:red;font-size:12px;font-weight:normal;">
                <strong
                    id="void-time{{ $receipt->id ?? '' }}">{{ \Carbon\Carbon::parse($receipt->voided_at)->format('dMy H:i:s') ?? '' }}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left" style="color:red;font-size:12px;font-weight:normal;">
                <strong id="void-reason{{ $receipt->id ?? '' }}">{{ $receipt->void_reason ?? '' }}</strong>
            </div>
        </div>
    </div>



<!--section 4 start-->

</div>
</body>
</html>
