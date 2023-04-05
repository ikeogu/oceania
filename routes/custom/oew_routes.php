<?php
// Custom Self Service Routes
Route::get('/outdoor-ewallet/{date?}', 'OutdoorEwalletController@OutdoorEwalletList')->name('outdoor-ewallet-list');
Route::get('/outdoor-e-wallet-provider-information', 'OutdoorEwalletController@OutdoorEWalletProviderInformation')->name('outdoor.e.wallet.provider.information');
Route::post('/oew-receipt','OutdoorEwalletController@oewReceipt')->name('oew.get_receiptlist');
Route::post('/oew-list-table','OutdoorEwalletController@dataTable')->name('oew-list-table');
Route::post('/oew-refund','OutdoorEwalletController@oewRefund')->name('oew.refund');
Route::post('/oew-receipt/update-filled','OutdoorEwalletController@updateFilled')->name('oew.update.filled');
?>
