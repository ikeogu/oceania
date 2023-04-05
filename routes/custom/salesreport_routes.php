<?php
// Custom routes for Oceania Fuel Sales Report
Route::get('sales/report','SalesReportController@generate')->
    name('sales.report');

/*
Route::post('sales/print/pdf','SalesReportController@printPDF')->
    name('sales.report.print.pdf');
*/

Route::post('sales/fuel/print/pdf','SalesReportController@fuelPrintPDF')->
    name('sales.fuel.report.print.pdf');

Route::any('sales/ev/print/pdf','SalesReportController@evPrintPDF')->
    name('sales.ev.report.print.pdf');

Route::any('sales/h2/print/pdf','SalesReportController@h2PrintPDF')->
    name('sales.h2.report.print.pdf');

Route::any('sales/oew/print/pdf','SalesReportController@oew_print_pdf')->
    name('sales.oew.report.print.pdf');

Route::any('sales/opt/print/pdf','SalesReportController@opt_print_pdf')->
    name('sales.opt.report.print.pdf');

Route::get('sales/terminal/date','SalesReportController@terminalDate')->
    name('sales.terminal.date');

Route::post('sales/report/export/excel',
    'SalesReportController@storeExcel')->name("sales.storeExcel");

Route::post(
    '/fuel_summary',
    'SalesReportController@generate_fuelsales_summary_pdf'
)->name("fuel_summary");

?>
