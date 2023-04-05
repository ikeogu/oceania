<?php
	/* Custom routes for Returning module */

Route::get('cstore_returning_list','ReturningController@cstore_returning_list')->
    name('cstore_returning_list');

Route::get('display_returning_list','ReturningController@display_returning_list')->
    name('returning.display_returning_list');

Route::get('cstore_returning', 'ReturningController@cstore_returning')->
    name('cstore_returning');

Route::post('display_returing_note', 'ReturningController@display_returing_note')->
    name('returning_list.datatable');

Route::post('save_returning_qty','ReturningController@save_returning_qty')->
    name('returning.save_returning_qty');

Route::get('returning_note_report/{systemid}', 'ReturningController@returning_note_report')->
    name("returning.stock_report");

Route::get('returning_notes.confirmed_datatable', 'ReturningController@confirmed_datable')->
    name('returning_notes.confirmed_datatable');

