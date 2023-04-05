<?php

Route::get("/creditaccount", "CreditAccountController@creditAccount")->name("creditaccount.get");
Route::post("/creditaccount/list", "CreditAccountController@creditAccountList")->name("creditaccount.list");
Route::get("/credit/account/clist", "CreditAccountController@CList")->name("Clist.get");
Route::post("/creditaccount/list_ledger/{systemid}", "CreditAccountController@creditAccountListLedger")->name("creditaccount.listledger");
Route::post("/creditaccount/listMerchantActive", "CreditAccountController@listMerchantActive")->name("creditaccount.listMerchantActive");
Route::post("/creditaccount/receiptCreditAction", "CreditAccountController@receiptCreditAction")->name("creditaccount.receiptCreditAction");
Route::post("/creditaccount/receiptCreditActionUpdate", "CreditAccountController@receiptCreditActionUpdate")->name("creditaccount.receiptCreditActionUpdate");
Route::get("/credit/acount/ledger/{id}", "CreditAccountController@CreditAccountLedger")->name("creditacountledger.get");
Route::post("/credit/acount/get_ledger/datatable/", "CreditAccountController@CreditAccountLedgerDatatable")->name("creditacountledger.datatable");
Route::post("/credit/acount/ledger/newPayment", "CreditAccountController@newPayment")->name("creditact.newPayment");
Route::post("/credit/acount/merchant/new_merchant", "CreditAccountController@save_merchant")->name("creditact.save_merchant");
Route::post("/credit/acount/merchant/save_credit_limit", "CreditAccountController@save_credit_limit")->name("creditact.save_credit_limit");
Route::post("/credit/acount/merchant/delete_merchant", "CreditAccountController@delete_merchant")->name("creditact.delete_merchant");
Route::post("/credit/acount/merchant/check_merchant_account", "CreditAccountController@check_merchant_account")->name("creditact.check_merchant_account");
Route::post("/credit/acount/merchant/download_ca_pdf_stmt", "CreditAccountController@download_ca_pdf_stmt")->name("creditact.download_ca_pdf_stmt");
