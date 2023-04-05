<?php
// Custom routes for Oceania CStore Sales Report

Route::post('sales/cstore/print/pdf','CStoreSalesReportController@printPDF')->
    name('sales.cstore.report.print.pdf');
?>
