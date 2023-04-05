<?php
// Custom routes for Receiving Note
Route::get('cstore_audited_notes_list', 'CStoreAuditedRptController@getAuditedNotes')->name('cstore_audited_notes_list');

Route::get('listPrdAuditedRpt', 'CStoreAuditedRptController@listPrdAuditedRpt')->name('cstore.listPrdAuditedRpt');
Route::post('listAuditedRpt', 'CStoreAuditedRptController@listAuditedRpt')->name('cstore.listAuditedRpt');


Route::get('audited_notes', 'CStoreAuditedRptController@getAuditedList')->name('audited_notes');
Route::get('audited_note_report/{id}', 'CStoreAuditedRptController@view_audited_report')->
    name('audited_note_report');
Route::post('confirmed_audited_report', 'CStoreAuditedRptController@confirmed_datatable')->
    name('cstore.confirmed_auideted_list');

Route::post('update_audited_notes', 'CStoreAuditedRptController@updateAuditReport')->
    name('update_audited_notes');

// Mobile view
Route::get('mob-audited-rpt', 'CStoreAuditedRptController@mobileView')->
    name('mob-audited-rpt');
