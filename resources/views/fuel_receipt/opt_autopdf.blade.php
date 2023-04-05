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
br{
    margin:5px !important;
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
    margin-top: 5px !important;
    margin-bottom: 5px !important;
    border: 0;
    border-top: 0.5px solid #c0c0c0;
    border-style: solid;
    width: 92% !important;
    position: fixed;
  }

  .line {
    padding-top: 5px !important;
    padding-bottom: 5px !important;
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

    }
</style>
</head>
<body>


<div class="rec_id" style="font-size: 14px; font-weight: bold;"
	id="{{ $sale->saleId }}">
    <!--Section 1 starts-->
    <table border="0" cellpadding="0" cellspacing="0" class="table" id="item"
		style="width:100%;margin-top: 5px !important;padding-right:10px;">
        <tr>
            <td style="text-align: center;font-weight: normal">
                <span style="font-weight: normal">
				@php
				$orgDate = DateTime::createFromFormat('d/m/Y H:i:s',
					$sale->transactionTime);
				$ttime = $orgDate->format('dMY H:i:s');
				@endphp
				{!!	$ttime !!}
                </span>
            </td>
        </tr>
    </table>
    <div class="row" style="text-align:center;">
        <div class="col-md-12 text-center pl-5 pr-5" style="font-size: 17px">
            <b>INVOICE</b>           
        </div>
    </div>
   
    <hr class="solid">
    @php
    $sale_receipt = strtr ($sale->receipt, array (
		'{n}' => '',
		'>>' => '',
		'{f1}' => '',
		'{f12}' => '',
		'{b}' => '',
		'{/b}' => ''
	));
    
    $pattern = '/APPROVED [0-9 ]*/';
    $replacement = "<b>$0</b>";
    $sale_receipt = preg_replace($pattern, $replacement, $sale_receipt);

	// TODO: may need to tweak for final production
	$saleItems = (object)$sale->saleItems[0];

	dump($saleItems->productId);
	dump($product);
    @endphp
   
    <table border="0" cellpadding="0" cellspacing="0" class="table" id="item"
		style="width:100%;margin-top: 0 !important;padding-right:10px;">
		<tr>
			<td style="text-align: right;font-weight: normal"></td>
			
				<span style="font-weight: normal">
				<pre>{!! $sale_receipt !!}</pre></span>
			</td>
		</tr>
    </table>
    
    <table border="0" cellpadding="0" cellspacing="0" class="table" id="item"
		style="width:100%;margin-top: 0 !important;padding-right:10px;">
		<tr>
			<td style="text-align: left;font-weight: normal">
			<span style="font-weight: bold;font-size:12px;color:gray;">
				{{$product[$saleItems->productId] }}
			</span></td> 
			<td style="text-align: leftt;font-weight: normal">
				<span style="font-weight: bold;color:gray;font-size:12px;">
					{{$saleItems->amount }}
				</span>
			</td>
		</tr>
		<hr class="solid" style="margin-bottom:10px !important;">
		<hr style="border:0px !important;padding:6px !important;margin:0px !important;">
        <tr>
           <td style="text-align: left;font-weight: normal"><span style="font-weight: normal;font-size:12px">TOTAL</span></td>
            <td style="text-align: right;font-weight: normal">            
                <span style="font-weight: normal">
                {{	$sale->amount }}</span>
            </td>
        </tr>
        <hr class="solid">
    </table>
</div>
<br/>
<div class="row d-flex" style="justify-content:center">
    <div style="font-size:14px" class="text-center">
        Thank You for visiting SEDC
    </div>
</div>

<!--section 4 start-->

</div>
</body>
</html>
