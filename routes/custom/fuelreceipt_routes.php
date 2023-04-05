<?php
// Custom routes for Fuel Receipt processing

Route::post('/fetch-latest-receipt-id', 'FuelReceiptController@fetch_latest_receipt_id')->name('fuel.fetch-latest-receipt-id');
Route::get('/fetch-latest-receipt-id/{my_pump}', 'FuelReceiptController@fetch_latest_receipt_id')->name('nfuel.fetch-latest-receipt-id');

Route::get('/fuel-receipt-list/{date}', 'FuelReceiptController@fuelReceipList')->name('fuel-receipt-list');
Route::get('/recall-list', 'FilledRecoveryController@index')->name('recall-list');
Route::get('/pump-get-status-x', function () {
    return json_decode("{\"data\":{\"http_code\":200,\"response\":{\"Packets\":[{\"Data\":{\"LastAmount\":100,\"LastNozzle\":1,\"Pump\":3,\"LastPrice\":7.77,\"LastTransaction\":8,\"LastVolume\":12.87}}],\"Type\":\"PumpIdleStatus\"}}}");
});
Route::post('/recall-list/update', 'FilledRecoveryController@updatePumpRecord')->name('recall-list.update');
Route::post('/create-fuel-list', 'FuelReceiptController@CreateFuelList')->name('create-fuel-list');
Route::post('/fuel-list-table', 'FuelReceiptController@dataTable')->name('fuel-list-table');
Route::post('/recall-list-table', 'FilledRecoveryController@datatableFilledRecovery')->name('recall-list-table');
Route::post('/fuel-receipt', 'FuelReceiptController@fuelReceipt')->name('fuel.envReceipt');
Route::post('/fuel-refund', 'FuelReceiptController@fuelRefund')->name('fuel.refund');
Route::post('/fuel-receipt/voided', 'FuelReceiptController@voidedReceipt')->name('fuel.voidReceipt');
Route::post('/fuel-receipt/update-filled', 'FuelReceiptController@updateFilled')->name('update.filled');
Route::post('/fuel-receipt/search-pump', 'FuelReceiptController@datatable_search_pump')->name('fuel.search.pump');
Route::post('/fuel-receipt/search-ptype', 'FuelReceiptController@datatable_search_ptype')->name('fuel.search.ptype');
?>
