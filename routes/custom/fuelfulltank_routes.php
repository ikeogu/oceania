<?php
	// Custom routes for Fuel Full Tank functionality
	// Route::get('/ft-fuel-receipt-list/{date}', 'FuelFulltankController@fuelReceipList')->name('ft-fuel-receipt-list');
	Route::post('/ft-fuel-receipt','FuelFulltankController@FTfuelReceipt')->name('fulltank.envReceipt');
	Route::get('/ft-fuel-receipt-list/{date}', 'FuelFulltankController@FTfuelReceipList')->name('ft-fuel-receipt-list');
	Route::post('/ft-create-fuel-list','FuelFulltankController@FTCreateFuelList')->name('ft-create-fuel-list');
	Route::post('/ft-fuel-list-table','FuelFulltankController@ft_dataTable')->name('ft-fuel-list-table');
	Route::post('/ft-print-receipt', 'PrintController@print_fulltank_receipt')->name('ft-print-receipt');
	Route::post('/ft_get_sync_data', 'FuelFulltankController@ftGetData')->name('ft_get_sync_data');
	Route::post('/ft_delete_sync_data', 'FuelFulltankController@ftDeleteData')->name('ft_delete_sync_data');
	Route::post('/sync-data-ft', 'FuelFulltankController@ftSyncData')->name('sync-data-ft');
	Route::post('/ft-fuel-receipt/search-pump','FuelFulltankController@ft_datatable_search_pump')->name('ft.fuel.search.pump');
	Route::post('/ft-fuel-receipt/search-ptype','FuelFulltankController@ft_datatable_search_ptype')->name('ft.fuel.search.ptype');
?>
