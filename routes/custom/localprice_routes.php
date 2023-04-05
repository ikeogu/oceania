<?php
// Custom routes for OPOSsum Screen D Local Price
Route::get("/local_price", "LocalPriceController@local_price")->name("local_price.landing");

Route::post("/local_price/datatable", "LocalPriceController@local_price_datatable")->name("franchise.location_price.datatable");

Route::get("/local_price/barcode/{systemid}", "LocalPriceController@product_barcode")->name("franchise.location_price.barcode");

Route::post("/local_price/barcode/datatable/{systemid}", "LocalPriceController@product_barcode_datatable")->name("franchise.location_price.barcode.datatable");

Route::post("/local_price/price", "LocalPriceController@locationPriceUpdate")->name("franchise.location_price.price_update");

Route::get("/local_price/stock_in", "LocalPriceController@StockIn")->name("franchise.location_price.stock_in");

Route::get("/local_price/stock_out", "LocalPriceController@StockOut")->name("franchise.location_price.stock_out");

Route::get(
    '/local_price/f_product_json',
    'LocalPriceController@stockInDatatable'
)->name("franchise.location_price.prd_ajaxJson");

Route::post(
    '/local_price/activate_all',
    'LocalPriceController@priceToggleAll'
)->name("franchise.location_price.activate_all");

Route::post(
    'local_price/stockupdate',
    'CentralStockMgmtController@stockUpdate'
)->name('franchise.location_price.stockUpdate');

Route::post("/local_price/products/save_cost", "LocalPriceController@save_prd_cost")->name("local_price.save_prd_cost");

Route::get("/local_price/prodloc_cost", "LocalPriceController@prodloc_cost")->name("local_price.prodloc_cost");

Route::get("/local_price/locprod_cost/{systemid}", "LocalPriceController@locationproduct_cost")->name("local_price.locprod_cost");

Route::post("/local_price/locprod_cost_datatable", "LocalPriceController@locprod_cost_datatable")->name("local_price.locprod_cost_datatable");

Route::post('/local_price/locprod_cost/update', 'LocalPriceController@locprod_update_cost')->name('local_price.update_locprd_cost');

Route::post('/local_price/locprod_cost/qty-distribution', 'LocalPriceController@locprod_qty_distribution')->name('locprod.qty-distribution.datatable');

Route::get("/local_price/proledger/cost-distribution/{receipt_id}/{product_id}", "CentralStockMgmtController@cost_distribution_tbl");
