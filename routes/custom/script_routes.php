<?php
// Custom script routes
Route::get('/script/copy_fuel_receiptlist','ScriptController@copyFuelReceiptData')->name('copy.fuel.receipt.name');

// seedProductCostTables
Route::get('/script/seed_cost_productledger_table', 'ScriptController@seed_cost_productledger_table')->name('seed.cost_productledge.table');

Route::get('/script/populate_cstore_receiptdetails', 'ScriptController@populate_cstore_receiptdetails_table')->name('seed.cstore_receiptdetails.table');
?>
