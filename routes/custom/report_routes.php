<?php
    Route::get(
        '/report',
        'ReportController@getReport'
    )->name("report");
    Route::post(
        '/cost_value_report',
        'ReportController@cost_value_reportPDF'
    )->name("cost_value_report");

    Route::post(
        '/c-storePL_report',
        'ReportController@cstorePLPDF'
    )->name("c-storePL");
