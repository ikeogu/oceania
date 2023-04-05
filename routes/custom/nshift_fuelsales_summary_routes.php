<?php
// Custom routes for NShift FuelSales Summary Report
Route::post('download/nshift','NShiftController@get_nshift_details_from_id')->
    name('download.nshift.pdf');
?>
