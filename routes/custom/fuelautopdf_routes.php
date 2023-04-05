<?php
	// Custom routes of Fuel Auto PDF
    Route::post("/generate_pdf_receipt", "FuelAutoPDFController@generate_pdf_receipt")->
        name("receipt.generatepdf");
    Route::post("/generate_void_pdf", "FuelAutoPDFController@generate_void_pdf")->
        name("receipt.generatevoidpdf");
