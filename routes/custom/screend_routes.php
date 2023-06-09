<?php
	//Custom Routes for Screen D
    Route::get('/screen_d',
	'ScreenDController@screen_d')->name("screen.d");
    Route::get('/screen_oceania_d',
	'ScreenDController@screen_oceania_d')->name("screen.d.oceania");
    Route::get('/local_fuelprice',
    'ScreenDController@local_fuelprice')->name("local.fuelprice");
    Route::get('/fuel_movement',
    'ScreenDController@fuel_movement')->name("fuel.movement");
    Route::get('/setting',
    'ScreenDController@settingindex')->name("settin.index");
    Route::get('/currency_model',
    'ScreenDController@getCurrency')->name("screen.d.currency");

	// Disable setting currency
	/*
    Route::post('/currency_model',
    'ScreenDController@setCurrency')->name("screen.d.currency");
	*/

    Route::get('/rate_model',
    'ScreenDController@getRate')->name("screen.d.rate");

	// Disable setting GST rate
	/*
    Route::post('/rate_model',
    'ScreenDController@setRate')->name("screen.d.rate");
	*/


    Route::post('/setting/savelogo',
    'ScreenDController@setLogo')->name("screen.d.setting.savelogo");
    Route::get('/setting/dellogo',
    'ScreenDController@delLogo')->name("settings.delLogo");
    Route::post('/setting/saveCompanydetails',
    'ScreenDController@saveCompanydetails')->name("screen.d.setting.saveCompanydetails");
    Route::post('/setting/saveLocationName',
    'ScreenDController@saveLocationName')->name("screen.d.setting.saveLocationName");
    Route::post('/setting/saveTime',
    'ScreenDController@saveTime')->name("screen.d.setting.savetime");
    Route::post('/setting/dltDirector',
    'ScreenDController@dltDirector')->name("screen.d.setting.dltdirector");
    Route::post('/setting/dltContact',
    'ScreenDController@dltContact')->name("screen.d.setting.dltcontact");
    Route::get('/fuel_stock_in',
    'FuelMovementController@fuelStockIn')->name("fuel.stockIn");
    Route::get('/fuel_product_json',
    'FuelMovementController@fuelProduct')->name("fuelProduct.ajaxJson");
	Route::get('/fuel_stock_out',
    'FuelMovementController@fuelStockOut')->name("fuel.stockOut");
    Route::get('/cstore/{amount?}/{product_id?}/{selected_pump?}',
    'ScreenDController@cstore')->name("index.cstore");
    Route::get('/getproduct/{product_id?}/',
    'ScreenDController@getproduct')->name("index.getproduct");

    Route::get('/fuel_guide',
    'FuelMovementController@fm_guide')->name("fuel.guide");

    // Setting for auto stockin product
    Route::get('/setting/autostockin_turn_on', 'ScreenDController@turn_on_auto_stockin_setting')->name("screend.setting.autostockin.turn_on");
    Route::get('/setting/autostockin_turn_off', 'ScreenDController@turn_off_auto_stockin_setting')->name("screend.setting.autostockin.turn_off");
?>
