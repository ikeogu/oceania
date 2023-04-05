<?php
	//Custom Routes for Printing
    Route::post('/print_receipt', 'PrintController@print_receipt')->name("receipt.print");
	Route::post('/eod_print', 'PrintController@eod_print')->name('local_cabinet.eod.print');
	Route::post('/pss_print', 'PrintController@PersonalShiftPrint')->name('local_cabinet.pss.print');
	Route::post('/fuel_print', 'PrintController@print_fuel_receipt')->name('receipt.fuel.print');
	Route::post('/h2_print', 'PrintController@print_h2_receipt')->name('receipt.h2.print');
	Route::post('/ev_print', 'PrintController@print_ev_receipt')->name('receipt.ev.print');
	Route::post('/oew_print', 'PrintController@print_oew_receipt')->name('receipt.oew.print');

?>
