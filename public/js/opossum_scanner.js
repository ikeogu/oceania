
/*
// This is for pre-fetching cache
$(document).ready(function() {
	fetch_products();
});
*/

function add_scanned_keycode(e, keys) {
	console.log(keys.join(""));
	add_product_scanner(keys.join(""))
	keys.length = 0;
	flag = 0;
	index = 0;
}


function add_product_scanner(product_barcode) {
	$.post("cstore/barcode-fetch-product", {"search_string":product_barcode})
	.done(function(res) {
		console.log('info', 'add_product_scanner: After AJAX: res='+
			JSON.stringify(res));

		// Test for "Barcode not found" error
		if (!res.error) {

			// Yes, add product to item list
			add_product(res.id, res.systemid, res.thumbnail_1, res.name, 1, res.price);

		} else {
			// That was an illegal/invalid barcode
			$("#message").html("Barcode not found");
			$("#messageModal").modal('show');
			setTimeout(function () {
				$("#messageModal").modal('hide')
				$("#message").html('')
			}, 2000);
		}

	}).fail(function(res)	{
		$("#message").html("Scanning error");
		$("#messageModal").modal('show');
		setTimeout(function () {
			$("#messageModal").modal('hide')
			$("#message").html('')
		}, 2000);
	});
}

/*/
function add_product_scanner(product_barcode) {
	fetch_products();
	/*
    console.log('product_barcode='+product_barcode);
    console.log('barcodes='+JSON.stringify(barcodes));


	 setTimeout(function () {
			barcodes = psystemid;
			if(barcodes.includes(product_barcode)) {
				addDProduct(pid[barcodes.indexOf(product_barcode)],undefined,undefined, true);
			} else {
				$("#msgModal").modal('show');
				setTimeout(function () {
					$("#msgModal").modal('hide')
				}, 2000);
			}
	}, 1000);
}
/*/

function display_product(prd) {
	alert(prd);
}
