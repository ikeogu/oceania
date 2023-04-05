<?php
// Custom H2 Local Fuel Price routes 
Route::get('/local_hydrogenprice',
    'H2LocalFuelPriceController@local_hydrogenprice')->name("local.hydrogenprice");

Route::post('h2-local-prices/datatable',
    'H2LocalFuelPriceController@showH2LocalPriceDatatable')->
    name('get_industry_oil_gas_h2_local_price_datatable');

Route::post('h2-local-prices/update',
    'H2LocalFuelPriceController@showH2LocalPriceUpdate')->
    name('get_industry_oil_gas_h2_local_price_update');