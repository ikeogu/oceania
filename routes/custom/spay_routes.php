<?php
// Custom routes for SPay Global

// These are for INDOOR payment
Route::post('/spay/spay_create_order',
	'SPayController@SPayCreateOrder')->
	name('wallet.spay.create_order');

Route::match(array('GET','POST'),'/spay/query_order',
	'SPayController@query_order')->
	name('spay.query_order');
?>
