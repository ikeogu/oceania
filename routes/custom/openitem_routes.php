<?php
// Custom Open Item routes
Route::get("/openitem", "OpenitemController@openitem")->name("openitem.openitem");

Route::get("/openitem/save", "OpenitemController@save")->name("openitem.save");

Route::post("/openitem/list", "OpenitemController@listPrdOpenitem")->name("openitem.list");

Route::post("/openitem/detail_product", "OpenitemController@detailProduct")->name("openitem.detail_product");

Route::post("/openitem/updatecustom", "OpenitemController@updateCustom")->name("openitem.updatecustom");

Route::get("/openitem/get_dropDown/{OPTION}/{KEY}", "OpenitemController@get_dropDown")->name("openitem.get_dropDown");

Route::post("/openitem/delPicture", "OpenitemController@delPicture")->name("openitem.delPicture");

Route::post("/openitem/savePicture", "OpenitemController@savePicture")->name("openitem.savePicture");

Route::post("/openitem/update_open", "OpenitemController@updateOpen")->name("openitem.update_open");

Route::post("/openitem/delete", "OpenitemController@deleteOpen")->name("openitem.delete");

Route::get("/openitem/prdledger/{systemid}", "OpenitemController@prdLedger")->name("openitem.prdledger");
Route::post("/openitem/prdledger_datatable/{systemid}", 'OpenitemController@prdLedger_datatable')->
    name('openitem.productLegder.datatable');

Route::get("/openitem/openitem_stockout", "OpenitemController@openitemStockout")->name("openitem.openitem_stockout");

Route::get("/openitem/openitem_stockin", "OpenitemController@openitemStockin")->name("openitem.openitem_stockin");

Route::post("/openitem/openitem_stockin_update", "OpenitemController@openitem_stock_update")->name("openitem.openitem_stockin_update");

Route::post("/openitem/openitem_stockout_update", "OpenitemController@openitem_stock_update")->name("openitem.openitem_stockout_update");

Route::post("/openitem/openitem_stockoutlist", "OpenitemController@stockOutList")->name("openitem.openitem_stockoutlist");

Route::post("/openitem/openitem_stockinlist", "OpenitemController@stockInList")->name("openitem.openitem_stockinlist");

Route::post("/openitem/products/save_cost", "OpenitemController@save_prd_cost")->name("openitem.openitem_save_prd_cost");

Route::post("/openitem/products/has_qty", "OpenitemController@product_has_qty")->name("openitem.openitem_has_qty");

Route::get("/openitem/products/barcode/{systemid}", "OpenitemController@product_barcode")->name("openitem.openitem_product_barcode");

Route::post('/openitem/products/selected', "OpenitemController@select_barcode_record")->name("openitem.record.selected");

Route::get('/openitem/product/barcode/generate', "OpenitemController@generate_product_bar_code")->name("openitem.barcode.generate");

Route::post('/openitem/product/barcode/delete', "OpenitemController@delete_product_barcode")->name("openitem.barcode.delete");

Route::post('/openitem/product/barcode/update_barcode_sku', 'OpenitemController@update_barcode_sku')->name('openitem.barcode.update_sku');

Route::post('/openitem/product/barcode/update_barcode_name', 'OpenitemController@update_barcode_name')->name('openitem.barcode.update_name');

Route::post('/openitem/product/savefifo', 'OpenitemController@saveFifo')->name('openitem.save.fifo');

Route::post('/openitem/product/saveexpiry', 'OpenitemController@saveExpiry')->name('openitem.save.expiry');

Route::post('/openitem/product/barcode_table', 'OpenitemController@show_barcode_table')->name('openitem.barcode.table.show');

Route::post('/openitem/product/save_barcode', 'OpenitemController@save_barcode')->name('openitem.barcode.save');

Route::post('/openitem/product/barcode/create_from_input_range', 'OpenitemController@create_barcode_from_input_range')->name('openitem.barcode.create_from_input_range');

Route::get('/openitem/openitem_cost/{systemid}', 'OpenitemController@openitem_cost')->name('openitem_cost');

Route::post('/openitem/openitem_cost/update', 'OpenitemController@openitem_update_cost')->name('openitem.update_cost');

Route::post('/openitem/openitem_cost/datatable', 'OpenitemController@openitem_cost_datatable')->name('openitem_cost.datatable');

Route::get('update_openitem_cost_when_auto_stockin_off', 'OpenitemController@update_openitem_cost_when_auto_stockin_off');

Route::post('/openitem/qty_distribution/datatable', 'OpenitemController@openitem_qty_population_datatable')->name('openitem.qty-distribution.datatable');

Route::get('/openitem/proledger/cost-distribution/{receipt_id}/{product_id}', 'OpenitemController@cost_distribution_tbl');

Route::get('/openitem/proledger/stockout/cost-distribution/{stockreport_id}', 'OpenitemController@cost_distribution_stockout');
