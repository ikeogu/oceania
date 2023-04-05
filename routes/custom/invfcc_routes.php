<?php
/*
Invenco ForeCourt Controller (FCC) callback for E1-100
Electronic Payment Server (EPS)
*/

Route::post('/invfcc/get_config',
	'InvFccController@get_config')->
	name('invfcc.get_config');

Route::post('/invfcc/get_fuelpoint_state',
	'InvFccController@get_fuelpoint_state')->
	name('invfcc.get_fuelpoint_state');

Route::post('/invfcc/reserve_fuelpoint',
	'InvFccController@reserve_fuelpoint')->
	name('invfcc.reserve_fuelpoint');

Route::post('/invfcc/free_fuelpoint',
	'InvFccController@free_fuelpoint')->
	name('invfcc.free_fuelpoint');

Route::post('/invfcc/authorize_fuelpoint',
	'InvFccController@authorize_fuelpoint')->
	name('invfcc.authorize_fuelpoint');

Route::post('/invfcc/terminate_fuelpoint',
	'InvFccController@terminate_fuelpoint')->
	name('invfcc.terminate_fuelpoint');

Route::post('/invfcc/lock_fuelsale',
	'InvFccController@lock_fuelsale')->
	name('invfcc.lock_fuelsale');

Route::post('/invfcc/clear_fuelsale',
	'InvFccController@clear_fuelsale')->
	name('invfcc.clear_fuelsale');

Route::post('/invfcc/get_delivery_details',
	'InvFccController@get_delivery_details')->
	name('invfcc.get_delivery_details');

/*
Route::post('/invfcc/fuelpoint_state_change_event',
	'InvFccController@fuelpoint_state_change_event')->
	name('invfcc.fuelpoint_state_change_event');

Route::post('/invfcc/fuelprice_change_event',
	'InvFccController@fuelprice_change_event')->
	name('invfcc.fuelprice_change_event');

Route::post('/invfcc/delivery_state_change_event',
	'InvFccController@delivery_state_change_event')->
	name('invfcc.delivery_state_change_event');

Route::post('/invfcc/delivery_started_event',
	'InvFccController@delivery_started_event')->
	name('invfcc.delivery_started_event');

Route::post('/invfcc/delivery_progress_event',
	'InvFccController@delivery_progress_event')->
	name('invfcc.delivery_progress_event');

Route::post('/invfcc/delivery_complete_event',
	'InvFccController@delivery_complete_event')->
	name('invfcc.delivery_complete_event');
*/
?>
