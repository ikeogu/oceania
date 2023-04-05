<?php
// Custom H2 routes
Route::get('/h2-landing', 'AccessController@one_host_landing_h2')->name('h2-landing');
Route::post('/get/pump/info', 'AccessController@h2PumpInfo')->name('h2-pump-info');
Route::get('h2-pump-get-status/{pump_no}/{ipaddr}',
	'H2Controller@h2PumpGetStatus');
Route::post('/h2_get_sync_data', 'H2Controller@h2GetData')->name('h2_get_sync_data');
Route::post('/sync-data-h2', 'H2Controller@h2SyncData')->name('sync-data-h2');
Route::get('pump-authorize-h2/{pump_no}/{type}/{dose}/{ipaddr}','H2Controller@pumpAuthorize');
Route::get('h2-pump-cancel-authorize/{pump_no}/{ipaddr}',
	'H2Controller@pumpCancelAuthorize');
	Route::post('/h2_delete_sync_data', 'H2Controller@h2DeleteData')->name('h2_delete_sync_data');
?>
