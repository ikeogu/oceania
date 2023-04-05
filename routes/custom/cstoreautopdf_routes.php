<?php
// Custom routes of CStore Auto PDF
Route::post('sales/cstore/pdf',
	'CStoreAutoPDFController@cstoreSalesPDF')->
	name('sales.cstore.generate.pdf');

Route::post("sales/cstore/void/pdf",
	"CStoreAutoPDFController@cstoreVoidSalesPDF")->
	name("cstore.generatevoidpdf");

Route::post("sales/cstore/refund/pdf",
	"CStoreAutoPDFController@cstoreRefundSalesPDF")->
	name("cstore.generaterefundpdf");
?>
