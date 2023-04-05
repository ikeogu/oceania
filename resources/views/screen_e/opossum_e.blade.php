<html>
	<head>
		<link href="{{ asset('css/bootstrap.css') }}" rel="stylesheet">
	    <link href="{{ asset('css/styles.css') }}" rel="stylesheet">

		<style>
			.main_div {
				width:100%;
				display:flex
			}

			.side_image_div {
				display:inline-flex;
				width: calc(100% - 500px);
				top: 0;
				bottom: 0;
				left: 0;
				right: 0;
				position: absolute;
				overflow: hidden;
			}

			.side_img {
				width: 100%;
				height: 100%;
				object-fit: cover;
			}
			.side_video {
				height: 100%;
				width: 100%;
				background-color: transparent;
				object-fit: cover;
			}
			.side_detail_div {
				width: 500px;
				background-color: {{ $location->e_right_panel_color ?? '#000000' }};
				height: 100%;
				right: 0;
				top: 0;
				position: absolute;
				border-left: 1px solid #fff;
				overflow: hidden;
				border-bottom: 20px solid {{ $location->e_bottom_panel_color ?? '#000000' }};
			}

			thead {
				background: {{ $location->e_table_header_color ?? '#000000' }};
				color: #fff;
			}
			tr {
				color: #fff;
				font-weight: 500;
				border: none;
			}
			td {
				border-top:none !important;
			}
			.footer_ {
				    opacity: 0.9;
				    position: absolute;
				    z-index: 999;
				    bottom: 14%;
				    left: 44%;
				    vertical-align: middle;
				    align-items: center;
				    display: none;
			}
			.footer-text {
				color: #fff;
				font-size: 70px;
				font-weight: 600;
			}
			.footer-span {
				color: #fff;
				display: block;
				/* transform: translateY(30px); */
				font-weight: 600;
				font-size: 36px;
				margin-bottom: -30px;
				margin-left: -20px;
			}
			.logo_div {
				position: absolute;
				bottom: 12%;
				right: 1%;
				width: 20%;
			}

			fuel_modl::before {
			  opacity: 0.2;
			}
		</style>
	</head>

	<body>
		<div class="main_div">
			<div class="side_image_div">
				@if (!empty($location->e_right_panel_image_file))
					@if (in_array(pathinfo($location->e_right_panel_image_file )['extension'],
							 ['mp4', '3gp', 'avi', 'flv', 'mpeg', 'webm', 'ogv', 'mpv']))
						<video class="side_video"
							width="100%" height="auto" muted loop autoplay  >
							<source src="/images/location/{{$location->id}}/{{$location->e_right_panel_image_file}}">
						</video>
					@else
						<img src="{{asset('images/location/' . $location->id . '/' .
							 $location->e_right_panel_image_file )}}" class="side_img" />
					@endif
				@endif
			</div>
			<div class="side_detail_div">
				<table class="table" id="my_table">
				<thead>
				<tr class="screen-e-table-heading-text">
					<th class="col-9" style="">Product</th>
					<th class="col-1" style="">Qty</th>
					<th class="col-2 text-right" style="">
					{{--
					Amount&nbsp;({{empty($cu->currency) ? 'MYR': $cu->currency }})</th>
					--}}
					{{empty($cu->currency) ? 'MYR': $cu->currency }}</th>
				</tr>


				</thead>
				<tbody>

				</tbody>
				</table>
			</div>

		<div class="footer_" id="total_amount"
			style=" border-radius: 6px;padding: 18px 0px 30px;text-align: center;color: white;line-height: 0.2;font-weight: 600;width: 403;height: 83;letter-spacing: 2px;font-size: 50px;display: none;background-color: {{ $location->e_bottom_panel_color ?? '#000000' }};">
		</div>
		@if(!empty($logo))
			<div class="logo_div">
				<img src="{{asset($logo)}}" style="width: 100%;" />
			</div>
		@endif

		<div class="modal fade" id="fuel_modal" tabindex="-1" role="dialog"
			style="padding-right:0 !important:width:90% !important"
			aria-labelledby="fuelModalLabel"
			aria-hidden="true">
		    <div class="modal-dialog modal-xl modal-dialog-centered" style="padding-right:0 !important" role="document">
		        <div class="modal-content modal-inside bg-purplelobster fuel_modl" style="height: 600px;" >
		            <div style="border:0" class="modal-header"></div>
		            <div class="modal-body text-center"
					style="display: flex;flex-direction: column;justify-content: center;align-items: center;"
					id="fuel_modal_body">

		            </div>
		            <div class="modal-footer"
						style="border-top:0 none; padding-left: 0px; padding-right: 0px;">
		                <div class="row p-0 m-0"
							style="padding-top:15px !important;
								padding-bottom:15px !important; width: 100%;">
		                </div>
		            </div>
		        </div>
		    </div>
		</div>


		</div>
		<input type="hidden" name="total_amount_landing" id="total_amount_landing" value="0">
		<input type="hidden" name="total_amount_pro" id="total_amount_pro" value="0">
	</body>

	<script src="{{ asset('js/jquery-3.4.1.min.js') }}"></script>
	<script src="{{ asset('js/bootstrap.js') }}"></script>

	<script>

	function calc_total_val(){
		full_amount = parseFloat($('#total_amount_landing').
			val())+parseFloat($('#total_amount_pro').val());
		var rowCount = $('#my_table tr').length;

		if(rowCount>1){
			$("#total_amount").html( '<p style="font-size:20px;letter-spacing: normal;text-align: center;margin-left: -214px;font-weight: normal;">Total</p>{{empty($cu->currency) ? 'MYR': $cu->currency }} '+full_amount.toFixed(2));
			$("#total_amount").show();

		}else{
			$("#total_amount").hide();
		}
	}

	var product_ids = JSON.parse("{{ $product_ids }}")
	var total_price = 0;

	document.addEventListener("DOMContentLoaded", function (event) {
		window.onstorage = function (e) {
		switch(e.key) {
			case "clear_products":
				$(`table tr.cstore_product`).remove();
				$('#total_amount_pro').val(0);
				calc_total_val();
				localStorage.removeItem('clear_products');
				break;

			case "delete_product":
				row_id = JSON.parse(localStorage.getItem('delete_product'));
				$(`table tr#product_row`+row_id.id).remove();
				$('#total_amount_pro').val(row_id.total);
				calc_total_val();
				localStorage.removeItem('delete_product');
				break;

			/*
			case "update-screen-e-landing":
				products = JSON.parse(localStorage.getItem('update-screen-e-landing'));
				console.log(products);
				html = '';
				total_price = 0;

					p = products;
					//$(`table tr#product_row${p.product_id}`).remove();
					html += `
						<tr id="product_row${p.product_id}">
							<td>
								<img
								src="${p.product_thumbnail}"
								style="width:25px;height: 25px;margin-right:8px;">
								<span class="text-white" style="width:30%">
									${p.name}
								</span>
							</td>

							<td>
								1
							</td>
							<td style="text-align:right;" >
								${p.dose}
							</td>
						</tr>
					`;
					total_price += parseFloat(p.dose);

				$('#total_amount_landing').val(total_price+ parseFloat($('#total_amount_landing').val()));
				jQuery('#my_table').append(html);
				calc_total_val();
				localStorage.removeItem('update-screen-e-landing')
				break;
			*/

			case "update-screen-e":
				products = JSON.parse(localStorage.getItem('update-screen-e'));
				//console.log(products);
				html = '';
				total_price = 0;
				for( i in products) {
					p = products[i];
					$(`table tr#product_row${p.product_id}`).remove();
					html += `
						<tr class="cstore_product" id="product_row${p.product_id}">
							<td>
								<img
								src="/images/product/${p.product_systemid}/thumb/${p.product_thumbnail}"
								style="width:25px;height: 25px;margin-right:8px;">
								<span class="text-white" style="width:40%">
									${p.name}
								</span>
							</td>

							<td style="width:10%">
								${p.qty}
							</td>
							<td style="text-align:right;" >
								${p.total_amount}
							</td>
						</tr>
					`;
					total_price += parseFloat(p.total_amount);
				}
				//console.log(html);
				jQuery('#my_table').append('');
				jQuery('#my_table').append(html);
				$('#total_amount_pro').val(total_price);
				calc_total_val();
				localStorage.removeItem('update-screen-e')
				break;

			case "show-screen-e-fuel-modal":
				product = JSON.parse(localStorage.getItem('show-screen-e-fuel-modal'));
				console.log("fuel product", product);
				jQuery('#fuel_modal_body').empty()
				html = '';
				html = `
					<div>
						<div class="col col-m-6 text-center">
							<h1 style="font-size:120px;">${product.product}&nbsp;${product.dose}</h1>
						</div>
					</div>
				`;

				jQuery('#fuel_modal_body').append('');
				jQuery('#fuel_modal_body').append(html);

				$("#fuel_modal").modal("show")
				localStorage.removeItem('show-screen-e-fuel-modal')

				setTimeout(function(){
				  	$('#fuel_modal').modal('hide')
			  	}, 5000);
				break;

			default:
		}}
	});

	</script>
</html>
