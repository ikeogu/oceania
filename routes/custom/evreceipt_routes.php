<?php
	// Custom routes for EV/Carpark Receipt processing
Route::get('/ev-receipt-list/{date?}', 'EVReceiptController@evReceipList')->name('ev-receipt-list');
Route::get('/ev-receipt-id', 'EVReceiptController@getEVReceipID')->name('ev-receipt-id');
Route::post('/create-ev-list','EVReceiptController@CreateEVList')->name('create-ev-list');
Route::post('/ev_receipt/evList','EVReceiptController@evList')->name('ev_receipt.evList');
Route::post('/ev_receipt/evListData','EVReceiptController@evListData')->name('ev_receipt.evListData');
Route::post('/ev_receipt/evReceiptDetail','EVReceiptController@evReceiptDetail')->name('ev_receipt.evReceiptDetail');
Route::post('/ev_receipt/evList','EVReceiptController@evList')->name('ev_receipt.evList');
Route::post('/ev-receipt/voided','EVReceiptController@voidedReceipt')->name('ev.voidReceipt');
?>
