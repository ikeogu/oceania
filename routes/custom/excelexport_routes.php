<?php
// Custom Excel Export Routes
Route::post(
    'download_fuel_receipt_list',
    'ExcelExportController@exportToExcelFuelReceiptlist'
)->name('export_excel_fuel_receipt');

Route::post(
    'download_fuel_fulltank_receiptList',
    'ExcelExportController@exportToExcelFuelFulltankReceiptList'
)->name('export_excel_fuel_fulltank_receiptlist');

Route::post('download_Cstore_excel', 'ExcelExportController@exportCstore')->name('export_cstore_excel');
Route::post('download_stock_ledger_excel', 'ExcelExportController@exportStockLedger')->
    name('export_stock_ledger_excel');

Route::post('download_nshift',
    'ExcelExportController@exportToExcelNshift')->name('export_excel_nshift');

Route::post(
    'download_returning_excel',
    'ExcelExportController@exportReturningNote'
)->name('returning.excel_download');

Route::post(
    'download_receiving_excel',
    'ExcelExportController@exportReceivingNote'
)->name('receiving.excel_download');

Route::post(
    'download_audited_report_excel',
    'ExcelExportController@exportAuditedReport'
)->name('audited_report.excel_download');
