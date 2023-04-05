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
                {{ !empty($receipt->currency) ? $receipt->currency : 'MVR' }}
            </strong>
        </div>
    </div>
    <hr style="width:92%;border: 0.5px solid #c0c0c0;margin-top:5px !important">
    @if (!empty($receiptproduct))
        @foreach ($receiptproduct as $product)
            <div class="row align-items-center" style="font-weight: normal">
                <div class="col-md-4 receipt-item-l">
                    {{ !empty($product->name) ? $product->name : 'RON95' }}
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
  padding: 0px;
  margin-left: -22px;
 ">
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
                    font-size:13px;border-radius:10px;" onclick="">
                <b>Loyalty</b>

            </button>
            <button class="nohover sellerbutton" style="position:relative;left:-5px;
				background-color:white;border:0;pointer-events:none">
				<img src="{{ asset('images/dispenser_icon.png') }}" style="filter:invert(100%);transform:scaleX(-1);
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

        <div class="col-md-12 d-flex" style="padding-left: 20px;">

            <button class="btn btn-success bg-receipt-print" style="padding-left:0;padding-right:0;
				margin-right: 6.5px !important;
				font-size:13px;border-radius:10px; " onclick="print_receipt_ft({{ $receipt->id }})">
                <strong>Print</strong>
            </button>

			<!--
            <button id="credit_button" class="btn btn-success opos-button-credit-ac"
				style="padding-left:0;padding-right:0; margin-right: 5px;
				width:70px;height:70px;
				font-size:13px;border-radius:10px;
				@if ($receipt->status === 'voided'  || $receipt->payment_type != 'creditac' || empty($receiptdetails->creditac)) background: #3a3535;
                cursor:not-allowed; @endif"
			@if ($receipt->payment_type != 'creditac' || empty($receiptdetails->creditac))
                    disabled="disabled"
                @else
                    onclick="listMerchantData({{ $receipt->id }})"
                @endif>
                <b>Credit<br>A/C</b>
            </button>
			-->

            <!--
                <button class="nohover sellerbutton"
                    style="background-color:white;border:0;pointer-events:none">
                </button>
                -->

            <button class="nohover sellerbutton" style="background-color:white;border:0;pointer-events:none">
            </button>
        </div>
    </div>
</div>

<div class="row d-flex" style="justify-content:center">
    <div style="font-size:14px">
        Thank You!
    </div>
</div>
<!--- void by --->


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
    function print_receipt_ft(receipt_id) {
		console.log('PR FT - print_receipt_ft()');
		console.log('PR FT - receipt_id=' + JSON.stringify(receipt_id));
		$.ajax({
			url: "/ft-print-receipt",
			headers: {
				'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
			},
			type: 'post',
			data: {
				'receipt_id': receipt_id,
			},
			success: function(response) {
				var error1 = false,
					error2 = false;
				console.log('PR FT RECEIPT ' + JSON.stringify(response));
				try {
					eval(response);
				} catch (exc) {
					error1 = true;
					console.error('ERROR eval(): ' + exc);
				}
			
				if (!error1) {
					try {
						escpos_print_template();
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
</script>
