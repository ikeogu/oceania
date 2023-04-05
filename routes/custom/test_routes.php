<?php
// Custom test routes 

Route::get('/test/get_all_terminals',
	'TestController@get_all_terminals')->
    name('test.invssi.get_all_terminals');

Route::get('/test/get_all_terminal_status',
	'TestController@get_all_terminal_status')->
    name('test.invssi.get_all_terminal_status');



//--------------------------------------------------------
Route::get('/test/spay_create_order',
	'TestController@spay_create_order')->
    name('test.spay.create_order');

Route::get('/test/invfcc_reserve_fuelpoint/{pump_no}',
	'TestController@invfcc_reserve_fuelpoint')->
    name('test.invfcc.reserve_fuelpoint');

Route::get('/test/invfcc_free_fuelpoint/{pump_no}',
	'TestController@invfcc_free_fuelpoint')->
    name('test.invfcc.free_fuelpoint');

Route::get('/test/invfcc_authorize_fuelpoint/{pump_no}/{amount}',
	'TestController@invfcc_authorize_fuelpoint')->
    name('test.invfcc.authorize_fuelpoint');

Route::post('/opt_terminal_data',
	'OptTerminalSyncController@optGetTerminalData')->
	name('opt.term.data');

?>
