<?php
// Custom routes for Receiving Note
Route::get("cstore_receiving_notes_list", "ReceivingNoteController@getReceivingNotes")->
    name("cstore_receiving_notes_list");

Route::get("receiving_notes", "ReceivingNoteController@getReceiveingList")->
    name("receiving_notes");
Route::post("receiving_notes/products", "ReceivingNoteController@get_datatable_products")->
    name("receiving_notes.get_datatable_products");

Route::post("receiving_notes/products/search_barcode", "ReceivingNoteController@search_barcode")->
    name("receiving_notes.search_barcode");

Route::get("receiving_list_id/{id}", "ReceivingNoteController@getReceivingNote")->
    name("receiving_list_id");

Route::post("update_receive_notes", "ReceivingNoteController@update_receive_notes")->
    name("update_receive_notes");
Route::post("/receiving_list/products/save_cost", "ReceivingNoteController@save_prd_cost")->
    name("receiving_list.save_prd_cost");

Route::post("receiving_notes/datatable", "ReceivingNoteController@get_receiving_note_datatable")->
    name("receiving_notes.datatable");

Route::post("receiving_notes/confirmed_datatable", "ReceivingNoteController@get_confirmed_note_datatable")->
    name("receiving_notes.confirmed_datatable");

Route::post("/receiving_list/get_product_id", "ReceivingNoteController@get_product_id")->
    name("receiving_notes.get_product_id");

Route::get("get_receiving_list", "ReceivingNoteController@displayReceievingNoteList")->
    name("receiving_list");
