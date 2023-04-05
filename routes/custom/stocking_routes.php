<?php
Route::get(
    'stocking/show-product-ledger-sale/{product_id}',
    'CentralStockMgmtController@showproductledger')->
	name('stocking.showproductledger');

Route::get('stocking/show-stock-report/{report_id}',
    'CentralStockMgmtController@showStockReport')->
    name('stocking.stock_report');

Route::post('stocking/show-product-ledger-sale_datatable/{systemid}',
	'CentralStockMgmtController@showproductledger_datatable')->
    name('stocking.showLocationProductDatatable');

Route::get('stocking/locprod/proledger/stockout/cost-distribution/{stockreport_id}',
    'CentralStockMgmtController@stockout_cost_dist')
?>
