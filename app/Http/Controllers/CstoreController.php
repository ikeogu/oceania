<?php

namespace App\Http\Controllers;

use Log;
use DB;
use Exception;
use Illuminate\Http\Request;
use App\Exceptions\Handler;


class CstoreController extends Controller
{
    function barcode_fetch_product(Request $request) {
	   try {
		   $search_string = $request->search_string;
		   $is_matrix = false;
		   $barcode = DB::table('productbarcode')->
			   where('barcode', $search_string)->
			   whereNull('deleted_at')->
			   first();

			if (empty($barcode)) {
				$barcode = DB::table('productbmatrixbarcode')->
					where('bmatrixbarcode',$search_string)->
					whereNull('deleted_at')->
					first();
				$is_matrix = true;
			}

			if (empty($barcode)) {
				throw new Exception("Barcode not found");
			}

			$product_id = $barcode->product_id ?? null;

			// If product is from prd_inventory
			$product = DB::table('product')->
				select('product.*', 'localprice.recommended_price as price')->
				join('localprice','localprice.product_id','product.id')->
				where('product.id', $product_id)->
				whereNull('product.deleted_at')->
				first();

			// If product is from prd_openitem
			if (empty($product)) {
			$product = DB::table('product')->
				select('product.*', 'prd_openitem.price as price')->
				join('prd_openitem','prd_openitem.product_id','product.id')->
				where('product.id', $product_id)->
				whereNull('product.deleted_at')->
				first();
			}

			// Convert price to MYR with money format
			if (!empty($product->price)) {
				$product->price = strval(number_format($product->price/100,2));
			}

			return response()->json($product);

		} catch (Exception $e) {
			Log::error([
				"Error: "	=>	$e->getMessage(),
				"Line: "	=>	$e->getLine(),
				"File: "	=>	$e->getFile()
			]);

			return response()->json([
			   "message"	=>	"Barocde not found",
			   "error"	=>	true
			]);

			//abort(404);
		}
	}
}
