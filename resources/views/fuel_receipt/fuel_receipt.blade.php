<!--Modal EodPrint-->
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

    }

    .opos-button-credit-ac {
        margin-top: 0 !important;
        margin-right: 0 !important;
        margin-left: 5px !important;
        margin-bottom: 5px !important;
        width: 70px !important;
        height: 70px !important;
        font-size: 16px;
        color: #ffffff;
        border-width: 0;
        border-radius: 10px;
        background-image: linear-gradient(#49f300, #bcf68c);
    }

    .opos-button-credit-ac-disabled {
        pointer-events: none !important;
        margin-top: 0 !important;
        margin-right: 0 !important;
        margin-left: 5px !important;
        margin-bottom: 5px !important;
        width: 70px !important;
        height: 70px !important;
        font-size: 16px;
        color: #ffffff;
        border-width: 0;
        border-radius: 10px;
        background-color: rgb(146, 146, 146);
        background-image: none;
    }

    .credit_button:hover {
        color: #34dabb;
        font-weight: bold;
    }

</style>

<!--Modal Body Starts-->

<div class="rec_id modal-body" style="font-size: 14px; font-weight: bold;" id="{{ $receipt->id }}">
    <!--Section 1 starts-->

    <div class="row" style="text-align:center;">
        <div class="col-md-12 text-center mt-4">
            @if (!empty($company->id) && !empty($receipt->receipt_logo))
                <img src="{{ asset('images/company/' . $company->id . '/corporate_logo/' . $receipt->receipt_logo) }}"
                    alt="" style="object-fit:contain;width: 80px; height: 80px;" srcset="" class="mr-1">
            @endif
        </div>
    </div>

    <div class="row" style="text-align:center;">
        <div class="col-md-12 text-center pl-5 pr-5" style="font-size: 17px">
            <b>
                {{ !empty($receipt->company_name) ? $receipt->company_name : '' }}
            </b>
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

    <hr style="border: 0.5px solid #c0c0c0;margin-top:5px !important;
  width:92%; font-weight:normal !important;" />

    <div class="row align-items-center">
        <div class="col-md-4 pr-0" style="font-weight:500 !important">
            <strong>Description</strong>
        </div>
        <div class="text-center col-md-2 pl-3 pr-0">
            <strong>Qty</strong>
        </div>
        <div class="text-center col-md-2 pl-3 pr-0">
            <strong class="global_currency">Price</strong>
        </div>
        <div class="text-center col-md-1 pl-3 pr-0">
            <strong>Disc.</strong>
        </div>
        <div class="text-right col-md-3 pl-0" style="font-size:17px">
            <strong id="item_amount">
                {{ !empty($receipt->currency) ? $receipt->currency : 'MYR' }}
            </strong>
        </div>
    </div>
    <hr style="width:92%;border: 0.5px solid #c0c0c0;margin-top:5px !important">
    @if (!empty($receiptproduct))
        @foreach ($receiptproduct as $product)
            <div class="row align-items-center" style="font-weight: normal">
                <div class="col-md-4 receipt-item-l">
                    {{ !empty($product->name) ? $product->name : '' }}
                </div>
                <div class="pl-3 col-md-2 receipt-item-c text-center" style="padding-left: 25px !important">
                    {{ !empty($product->quantity) ? number_format($product->quantity, 2) : '1' }}
                </div>
                <div class="pl-3 col-md-2 receipt-item-c text-center" style="padding-left: 25px !important">
                    <span
                        class="global_currency">{{ !empty($product->price) ? number_format($product->price / 100, 2) : '0.00' }}</span>
                </div>
                <div class="col-md-1 receipt-item-discount" style="padding-left: 22px !important">
                    {{ !empty($product->discount) ? $product->discount_pct : '0' }}%
                </div>
                <div class="col-md-3 receipt-item-r" style="
					padding: 0px; margin-left: -22px;">
                    <span id="item_amount">
                        {{ number_format($receiptdetails->total / 100, 2) }}
                        {{-- number_format((($receipt->cash_received/100-$receipt->cash_change/100)??"2"),2) --}}
                    </span>
                </div>
            </div>
        @endforeach
    @endif
    <hr style="width:92%;border: 0.5px solid #c0c0c0; margin-top:5px !important" />
    <div class="row">
        <div class="col-md-6" style="font-weight: normal">
            Item Amount
        </div>
        <div class="col-md-6" style="text-align: right;">
            <span style="font-weight:normal" id="item_amount">
                {{ number_format($receiptdetails->item_amount / 100, 2) }}
            </span>
        </div>
    </div>

	<!--
    <div class="row" style="font-weight: normal;">
        <div class="col-md-6">
            {{ !empty($terminal->taxtype) ? strtoupper($terminal->taxtype) : 'SST' }}
            {{ (float) $receipt->service_tax ?? '6' }}%
        </div>
        <div class="col-md-6" style="text-align: right;font-weight: normal;">
            <strong id="item_amount" style="font-weight: normal">
                {{ number_format($receiptdetails->sst / 100, 2) }}
            </strong>
        </div>
    </div>
	-->

    <div class="row" style="font-weight: normal">
        <div class="col-md-6">
            Rounding
        </div>
        <div class="col-md-6" style="text-align: right;">
            <strong id="rounding_item_amount" style="font-weight: normal">
                {{ number_format($receiptdetails->rounding / 100, 2) }}
                {{-- ($receipt->cash_received-$receipt->cash_change)%5==0?"0.00":((5 * round(($receipt->cash_received-$receipt->cash_change) / 5))-($receipt->cash_received-$receipt->cash_change))/100 --}}
            </strong>
        </div>
    </div>
    <div class="void-stamp" id="void-stamp{{ $receipt->id ?? '' }}" style="margin-left: 15% ;display:
   @if ($receipt->status == 'voided') block;
    @else
        none ; @endif ">
        VOID
    </div>

    <!--section 1 ends-->
    <hr style="width:92%;border: 0.5px solid #c0c0c0;margin-top:5px !important">
    <!--section 2 starts-->
    <div class="row">
        <div style="font-weight:normal" class="col-md-6">
            Total
        </div>
        <div class="col-md-6" style="text-align: right;">
            <span style="font-weight:normal" id="total_amount_unq">
                {{ number_format($receiptdetails->item_amount / 100 + $receiptdetails->sst / 100 + $receiptdetails->rounding / 100, 2) }}
            </span>
        </div>
    </div>
    <div class="row">
        <div style="font-weight:normal" class="col-md-6">
            Cash Received
        </div>
        <div class="col-md-6" style="text-align: right;">
            <span style="font-weight:normal" id="item_amount">
                @if ($receipt->payment_type == 'cash')
                    {{ !empty($receipt->cash_received) ? number_format($receipt->cash_received / 100, 2) : '0.00' }}
                @else 0.00
                @endif

            </span>
        </div>
    </div>
    <div class="row">
        <div style="font-weight:normal" class="col-md-6">
            Credit Card
        </div>
        <div class="col-md-6" style="text-align: right;">
            <span style="font-weight:normal" id="item_amount">
                @if ($receipt->payment_type == 'creditcard')

                    {{ !empty($receiptdetails->creditcard) ? number_format($receiptdetails->creditcard / 100 + (5 * round(($receiptdetails->creditcard - $receipt->cash_change) / 5) - ($receiptdetails->creditcard - $receipt->cash_change)) / 100, 2) : '0' }}
                @else 0.00
                @endif
            </span>
        </div>
    </div>

    <div class="row">
        <div style="font-weight:normal" class="col-md-6">
            Wallet
        </div>
        <div class="col-md-6" style="text-align: right;">
            <span style="font-weight:normal" id="item_amount">
                @if ($receipt->payment_type == 'wallet')
                    {{ !empty($receiptdetails->wallet) ? number_format($receiptdetails->wallet / 100 + (5 * round(($receiptdetails->wallet - $receipt->cash_change) / 5) - ($receiptdetails->wallet - $receipt->cash_change)) / 100, 2) : '0.00' }}
                @else 0.00
                @endif
            </span>
        </div>
    </div>
    <div class="row">
        <div style="font-weight:normal" class="col-md-6">
            Credit Account
        </div>
        <div class="col-md-6" style="text-align: right;">
            <span style="font-weight:normal" id="item_amount">
                @if ($receipt->payment_type == 'creditac')
                    {{ !empty($receiptdetails->creditac) ? number_format($receiptdetails->creditac / 100 + (5 * round(($receiptdetails->creditac - $receipt->cash_change) / 5) - ($receiptdetails->creditac - $receipt->cash_change)) / 100, 2) : '0.00' }}
                @else 0.00
                @endif
            </span>
            <input type="hidden" id="credit_account" value="
				@if ($receipt->payment_type == 'creditac')
                    {{ !empty($receiptdetails->creditac) ? number_format($receiptdetails->creditac / 100 + (5 * round(($receiptdetails->creditac - $receipt->cash_change) / 5) - ($receiptdetails->creditac - $receipt->cash_change)) / 100, 2) : '0.00' }}
                @else 0.00
                @endif">
        </div>
    </div>


    <!--section 2 ends-->
    <hr style="width:92%;border: 0.5px solid #c0c0c0; margin-top:5px !important">
    <!--section 3 starts-->
    <div class="row" style="font-weight: normal">
        <div class="col-md-6 text-left">
            <span style="font-weight: normal">Change</span>
        </div>
        <div class="col-md-6 text-right">
            {{ !empty($receipt->cash_change) ? number_format($receipt->cash_change / 100 - (5 * round(($receipt->cash_received - $receipt->cash_change) / 5) - ($receipt->cash_received - $receipt->cash_change)) / 100, 2) : '0.00' }}
        </div>
    </div>

    <!--section 3 ends-->
    <hr style="width:92%;border: 0.5px solid #c0c0c0; margin-top:5px !important">
    <div class="row">
        <div class="col-md-4 pr-0">
            <span style="font-weight: normal">Receipt No.</span>
        </div>
        <div class="col-md-8 pl-0 text-right">
            <span
                style="font-weight: normal">{{ !empty($receipt->systemid) ? $receipt->systemid : '7060000010000000014' }}</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 text-left">
            <span style="font-weight: normal">Location</span>
        </div>
        <div class="col-md-6 text-right" style="font-weight: normal">
            {{ $location->name ?? '' }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 text-left">
            <span style="font-weight: normal">Terminal ID</span>
        </div>
        <div class="col-md-6 text-right" style="font-weight: normal">
            {{ $terminal->systemid ?? '' }}
        </div>
    </div>

    <div class="row">
        <div class="col-md-6 text-left">
            <span style="font-weight: normal">Staff Name</span>
        </div>
        <div class="col-md-6 text-right" style="font-weight: normal">
            {{ $user->fullname ?? '' }}
        </div>
    </div>
    <div class="row">
        <div class="col-md-6 text-left">
            <span style="font-weight: normal">Staff ID</span>
        </div>
        <div class="col-md-6 text-right" style="font-weight: normal">
            {{ $user->systemid ?? '' }}
        </div>
    </div>

    <div class="row">
        <div class="col-md-12 pl-1 text-right">
            <span style="font-weight: normal">
                {{ date('dMy H:i:s', strtotime($receipt->created_at ?? '')) }}
            </span>
        </div>
    </div>


    <div class="row" style="text-align: center;margin: 10px auto;">
        <div class="col-md-12 d-flex" style="justify-content:space-around;margin-bottom:5px">

            <!--
                <button class="nohover sellerbutton"
                    style="background-color:white;border:0;pointer-events:none">
                </button>
                -->

            <button class="btn btn-success bg-receipt-loyalty" style="padding-left:0;padding-right:0; margin-right: 5px;
                    font-size:13px;border-radius:10px; @if ($receipt->status === 'voided') background: #3a3535;
                cursor:not-allowed; @endif"
                @if ($receipt->status === 'voided')
                    disabled="disabled"
                @else
                    onclick="" @endif>
                <b>Loyalty</b>

            </button>
            <button class="nohover sellerbutton" style="position:relative;left:0;
				 background-color:white;border:0;pointer-events:none">
                <img src="{{ asset('images/dispenser_icon.png') }}"
					style="filter:invert(100%);transform:scaleX(-1);
					width:50px;height:50px;object-fit:contain;
					margin-right:15px" />
            </button>

            <button class="nohover sellerbutton" style="position:relative;left:-7px;
				background-color:white;border:0;pointer-events:none">
                <span style="position:relative;left:-5px;
					background-color:white;
					font-weight:bold;color:black;font-size:40px;">
                    {{ !empty($receipt->pump_no) ? $receipt->pump_no : '' }}
                </span>
            </button>

            <img src="{{ asset('images/qr.png') }}" style="width: 70px;height: 70px;
				float: left;margin-bottom: 5px; border-radius: 10px;">
        </div>

        <div class="col-md-12 d-flex pl-10"
			style="margin: 0 5px;padding-left:10px">
            <button class="btn btn-success bg-receipt-print"
				style="padding-left:0;padding-right:0; margin-right: 5px;
				font-size:13px;border-radius:10px; "
				onclick="print_receipt({{ $receipt->id }})">
                <strong>Print</strong>
            </button>

			{{--
            <button class="btn btn-success btn-log bg-receipt-void
				sellerbutton void_true only_void_true only_void"
				style="padding-left:0; padding-right:0;  margin-right: 0;
				font-size:13px;border-radius:10px;
				@if ($receipt->status === 'voided' || $receipt->status === 'refunded')
					background: #3a3535;
					cursor:not-allowed;
				@endif"

                @if ($receipt->status === 'voided' || $receipt->status === 'refunded')
                    disabled="disabled"
                @else
                    onclick="void_receipt({{ $receipt->id }})"
				@endif>

                <strong>Void</strong>
            </button>


            <button id="credit_button" class="btn btn-success opos-button-credit-ac"
				style="padding-left:0;padding-right:0; margin-right:0;
				width:70px;height:70px;
				font-size:13px;border-radius:10px;
				@if ($receipt->status === 'voided'  || $receipt->payment_type != 'creditac' ||
					empty($receiptdetails->creditac)  || $receiptCount > 0)
					background: #3a3535; cursor:not-allowed;
				@endif"
                @if ($receipt->status === 'voided' || $receipt->payment_type != 'creditac' ||
					empty($receiptdetails->creditac) || $receiptCount > 0)
                    disabled="disabled"
                @else
                    onclick="listMerchantData({{ $receipt->id }})"
                @endif>
                <b>Credit<br>A/C</b>
            </button>
            --}}
			<button class="nohover sellerbutton"
				style="background-color:white;border:0;pointer-events:none">
			</button>

            <button class="nohover sellerbutton"
				style="background-color:white;border:0;pointer-events:none">
            </button>
        </div>
    </div>
</div>

<div class="row d-flex" style="justify-content:center">
    <div style="font-size:14px">
        Thank You!
    </div>
    <div class="text-center" style="font-size:12px;width:100%;
		margin-left:15px; margin-right:15px">
        This is the final receipt. Should there be a refund, the calculation below is applicable.
    </div>
</div>
<!--- void by --->
@if ($receipt->status === 'voided')

    <div id="void-div{{ $receipt->id ?? '' }}" style="display:@if ($receipt->status ==
    'voided') block @else none @endif" >
        <div class="row">
            <div class="col-md-4 text-left" style="color:red;">
                <strong>Voided by</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left" style="color:red;">
                <strong> {{ $receipt->user->fullname ?? '' }}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left" style="color:red;">
                <strong> {{ $receipt->user->systemid ?? '' }}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left" style="color:red;">
                <strong
                    id="void-time{{ $receipt->id ?? '' }}">{{ \Carbon\Carbon::parse($receipt->voided_at)->format('dMy H:i:s') ?? '' }}</strong>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6 text-left" style="color:red;">
                <strong id="void-reason{{ $receipt->id ?? '' }}">{{ $receipt->void_reason ?? '' }}</strong>
            </div>
        </div>
    </div>
@endif

@if ($refund && $receipt->status === 'refunded')
    <div class="container">
        <div style="font-size:14px;text-align:left; color:orange">
            Refunded By:
            <br>
            {{ $refund->fullname }}
            <br>
            {{ $refund->systemid }}
            <br>
            {{ date('dMy', strtotime($refund->created_at)) }}
            <br>
            MYR {{ number_format($refund->refund / 100, 2) }}
            <hr style="width:100%;border: 0.5px solid #c0c0c0;margin-top:5px !important">
            <div class="row">
                <div class="col-md-4 pr-0">
                    <span style="font-weight: normal">Item Amount</span>
                </div>
                <div class="col-md-8 pl-0 text-right">
                    <span style="font-weight: normal">
						{{number_format($refund->newsales_item_amount/100,2)}}
					</span>
                </div>
            </div>
			<!--
            <div class="row">
                <div class="col-md-4 pr-0">
                    <span style="font-weight: normal">
						{{ 'SST ' . (float) $receipt->service_tax ?? '6' }}%
					</span>
                </div>
                <div class="col-md-8 pl-0 text-right">
                    <span style="font-weight: normal">
						{{number_format($refund->newsales_tax/100,2)}}
					</span>
                </div>
            </div>
			-->
            <div class="row">
                <div class="col-md-4 pr-0">
                    <span style="font-weight: normal">Rounding</span>
                </div>
                <div class="col-md-8 pl-0 text-right">
                    <span style="font-weight: normal">
						{{number_format($refund->newsales_rounding/100,2)}}
					</span>
                </div>
            </div>
            <hr style="width:100%;border: 0.5px solid #c0c0c0;margin-top:5px !important">

        </div>
    </div>
@endif

<div class="row">
    <div class="col-md-12 text-right">
        <div style="font-size: 10px">
            <strong>Tetra Forecourt v1.0</strong>
        </div>
        <br>
    </div>
</div>


<!--section 4 start-->

</div>
<!--Modal Body ends-->
<div class="modal fade" id="listMerchantModal" tabindex="-1" role="dialog" aria-labelledby="staffNameLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered  mw-75 w-50" role="document">
        <div class="modal-content modal-inside bg-purplelobster">

            <div class="modal-header" style="font-size: 15pt">Credit Account</div>
            <hr>
            <div class="modal-body text-center">
                <div id="dataList" class="" style="widows: 100%; height: 300px; overflow-y: auto">

                </div>
            </div>

        </div>
        </ul>
    </div>
</div>
<script type="text/javascript">
    $(document).ready(function() {
        $('.sorting_1').css('background-color', 'white');
    });

    window.addEventListener('storage', (e) => {
        switch (e.key) {
            case "receipt_voided":
                console.log("voided: ", $('.rec_id').attr('id'))
                if (localStorage.getItem('receipt_voided1') === $('.rec_id').attr('id')) {
                    $.ajax({
                        method: "post",
                        url: "{{ route('fuel.envReceipt') }}",
                        data: {
                            id: localStorage.getItem('receipt_voided1')
                        }
                    }).done((data) => {
                        localStorage.removeItem('receipt_voided1');
                        //console.log(data);
                        $(".detail_view").html(data);

                    }).fail((data) => {
                        console.log("data", data)
                    });
                    // log2laravel('info', 'reload_for_fm_sales : reloaded fm sales');

                }

                localStorage.removeItem('receipt_voided')
                break;
            case "receipt_refunded":
                console.log("refunded: ", $('.rec_id').attr('id'))
                if (localStorage.getItem('receipt_refunded1') === $('.rec_id').attr('id')) {
                    $.ajax({
                        method: "post",
                        url: "{{ route('fuel.envReceipt') }}",
                        data: {
                            id: localStorage.getItem('receipt_refunded1')
                        }
                    }).done((data) => {
                        localStorage.removeItem('receipt_refunded1');
                        //console.log(data);
                        $(".detail_view").html(data);

                    }).fail((data) => {
                        console.log("data", data)
                    });
                    // log2laravel('info', 'reload_for_fm_sales : reloaded fm sales');

                }
                localStorage.removeItem('receipt_refunded1');
        }
    });
    /* Function to print receipt via 80mm thermal printer */
    function listMerchantData(receipt_id) {
        $.ajax({
            method: "post",
            url: "{{ route('creditaccount.listMerchantActive') }}",
        }).done((data) => {
            let dataList = data.data;
            console.log(data.data);

            $("#dataList").html("");
            let credit_account_amount = $("#credit_account").val();
            for (let i = 0; i < dataList.length; i++) {
                dataList[i]['type'] = "'"+dataList[i]['type']+"'";
                $("#dataList").append('<li class="p-2 text-left credit_button" style="width: 100%;list-style-type:none;cursor:pointer;" onclick="make_credit(' +
                    receipt_id + ' , ' + credit_account_amount + ','+dataList[i]['company_id']+','+dataList[i]['type']+')" > ' +
                    dataList[i]["name_company"] + ' </li>');
            }
            $("#listMerchantModal").modal("show");
        }).fail((data) => {
            console.log("data", data)
        });
    }


    function make_credit(receipt_id, creditac, company_id, type) {
         $.ajax({
		url: "{{route('creditaccount.receiptCreditAction')}}",
		headers: {'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')},
		type: 'post',
		data: {
			receipt_id:receipt_id,
			credit_ac:creditac,
            companyId:company_id,
            type:type,
		},
		success: function (response) {
			console.log('CA '+JSON.stringify(response));
            $("#listMerchantModal").modal('toggle');
            $(".opos-button-credit-ac").prop('disabled', 'disabled');
            $('#credit_button').removeClass('opos-button-credit-ac');
            $('#credit_button').addClass('opos-button-credit-ac-disabled');
            $('#credit_button').css('background-color', '#3a3535');
		},
		error: function (e) {
			console.log('CA '+JSON.stringify(e));
		}
	});
    }


    function print_receipt(receipt_id) {
        console.log('PR print_receipt()');

        $.ajax({
            url: "{{ route('receipt.fuel.print') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            data: {
                receipt_id: receipt_id,
            },
            success: function(response) {
                var error1 = false,
                    error2 = false;
                console.log('PR ' + JSON.stringify(response));

                try {
                    eval(response);
                    console.log('eval working');
                } catch (exc) {
                    error1 = true;
                    console.error('ERROR eval(): ' + exc);
                }

                if (!error1) {
                    try {
                        escpos_print_template();
                        console.log('template working');
                    } catch (exc) {
                        error2 = true;
                        console.error('ERROR escpos_print_template(): ' + exc);
                    }
                }
            },
            error: function(e) {
                console.log('PR ' + JSON.stringify(e));
            }
        });
    }


    /* Function to print receipt via 80mm thermal printer */
    function void_receipt(receipt_id) {
        $.ajax({
            url: "{{ route('fuel.voidReceipt') }}",
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            },
            type: 'post',
            data: {
                receipt_id: receipt_id,
            },
            success: function(response) {
                console.log(response);
                table.ajax.reload();
                $.ajax({
                    method: "post",
                    url: "{{ route('fuel.envReceipt') }}",
                    data: {
                        id: receipt_id
                    }
                }).done((data) => {
                    console.log('data');
                    $(".detail_view").html(data);
                    localStorage.setItem('receipt_voided', receipt_id);
                    localStorage.setItem('receipt_voided1', receipt_id);
                    localStorage.removeItem('receipt_voided')

                    localStorage.removeItem('reload_for_fm_sales')
                    localStorage.setItem("reload_for_fm_sales", "yes");
                }).fail((data) => {
                    console.log("data", data)
                });
            },
            error: function(e) {
                console.log('PR ' + JSON.stringify(e));
            }
        });
    }
</script>
