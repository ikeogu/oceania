<?php
// Custom H2 routes 


Route::get('/h2-receipt-list/{date?}', 'H2ReceiptController@h2ReceiptList')->name('date?}');
Route::post('/h2-list-table','H2ReceiptController@dataTable')->name('h2-list-table');
Route::post('/h2-receipt','H2ReceiptController@h2_Receipt')->name('h2.envReceipt');
Route::post('/h2-personal-shift-receipt','H2ReceiptController@h2PersonalShiftReceipt')->name('h2.envPersonalShiftReceipt');

Route::post('/h2-refund','H2ReceiptController@h2Refund')->name('h2.refund');
	
// Route::get('/h2-receipt-list/{date?}', 'H2ReceiptController@h2_receipt_list')->name('date?}');
Route::post('/h2-receipt-list-store', 'H2ReceiptController@store_h2_reciept_list')->name('store_h2_reciept_list');

?>
