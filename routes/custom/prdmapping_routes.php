<?php
	// stom route file for Product Mapping
    Route::get('product_mapping','ProductMappingController@product_mapping')->name('product_mapping');
    Route::post('product_mapping/save_mapping','ProductMappingController@save_mapping_info')->name('product_mapping.save_mapping');
