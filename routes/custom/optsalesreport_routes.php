<?php
// Custom routes for OPT Sales Report

Route::any('opt/sales/print/pdf','OPTSalesReportController@generate_opt_sales_pdf')->
    name('opt.sales.print.pdf');
?>
