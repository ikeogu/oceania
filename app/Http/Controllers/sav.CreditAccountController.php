<?php

namespace App\Http\Controllers;

use DB;
use App\Classes\SystemID;
use App\Models\Company;
use App\Models\MerchantLink;
use App\Models\MerchantLinkRelation;
use App\Models\Oneway;
use App\Models\Onewaylocation;
use App\Models\Onewayrelation;
use App\Models\User;
use Carbon\Carbon;
use Illuminate\Database\QueryException;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Yajra\DataTables\DataTables;
use PDF;

class CreditAccountController extends Controller
{
    function creditAccount()
    {
        try {
			DB::statement("SET SQL_MODE=''");
			$owner_user_id = DB::table('lic_locationkey')->
			  	join("company", "company.id", "lic_locationkey.company_id")->
				pluck('owner_user_id')->first();

			Log::debug('creditAccount: owner_user_id='.$owner_user_id);

			$getCompany = DB::table(DB::raw("merchantlink m, company c"))->
			  	select("c.*")->
				whereRaw ("(m.initiator_user_id = c.owner_user_id and m.responder_user_id = $owner_user_id)")->
				orWhereRaw("(m.initiator_user_id = $owner_user_id and m.responder_user_id = c.owner_user_id)")->
				orWhereRaw("(m.initiator_user_id = c.owner_user_id and c.owner_user_id = $owner_user_id)")->
				groupBy("c.id")->
				get();

			$data = $this->getAllData();
            $getoneway = Oneway::get();
            $getmerlink = new Oneway();

            $earliest_approved = DB::table('company')->
                whereNull('deleted_at')->
                orderBy('approved_at', 'desc')->
                first();
            $first_approved = strtotime($earliest_approved->approved_at);

			Log::debug('creditAccount: getCompany='.json_encode($getCompany));
			Log::debug('creditAccount: getoneway='.json_encode($getoneway));
            Log::debug('creditAccount: data='.json_encode($data));


            return view('credit_ac.credit_ac', compact(
				'getCompany',
				'getoneway',
				'getmerlink',
				'data',
				'first_approved'
			));

        } catch (Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);
            abort(404);
        }
    }


	function CList()
    {
        try {

            DB::statement("SET SQL_MODE=''");
			$owner_user_id = DB::table('lic_locationkey')->
			  	join("company", "company.id", "lic_locationkey.company_id")->
				pluck('owner_user_id')->first();

			Log::debug('clist owner_user_id='.$owner_user_id);


			$getCompany = DB::table('merchantlink')->
				join('company', function ($join) {
					$join->on('merchantlink.responder_user_id', '=',
						'company.owner_user_id')->
					orOn('merchantlink.initiator_user_id', '=',
						'company.owner_user_id');
				})->
				join('users', function ($join) {
					$join->on('merchantlink.responder_user_id', '=', 'users.id')->
						orOn('merchantlink.initiator_user_id', '=', 'users.id');
				})->
				join("merchantlinkrelation", "merchantlinkrelation.merchantlink_id",
					"merchantlink.id")->
					select('company.id','company.name','company.systemid','company.status',
						'company.corporate_logo')->
				where('company.owner_user_id','!=',$owner_user_id)->
				groupBy('company.id','company.name','company.systemid',
					'company.status','company.corporate_logo')->
				get();

            $creditact = DB::table('creditact')->
                join("fuel_receipt", "fuel_receipt.id", "creditact.fuel_receipt_id")->
                join("company","company.id", "creditact.company_id")->
                where("company.systemid",1020000000274)->
                select('creditact.*', 'fuel_receipt.systemid as document_number')->get();

			$data =array();

			foreach ($creditact as $item) {
				$date = date_create($item->created_at);
				array_push($data, [
					"date" => date_format($date,'dMy H:i:s'),
					"amount" => number_format($item->amount,2),
					"sysid" => $item->document_number,
					"receipt_id" => $item->fuel_receipt_id
				]);
			}

            $getoneway = Oneway::get();

			Log::debug('clist getCompany='.json_encode($getCompany));
            Log::debug('clist       data='.json_encode($data));
			Log::debug('clist  getoneway='.json_encode($getoneway));


            return view('credit_ac.credit_act_list_modal',
                compact('getCompany' , 'getoneway', 'creditact'));

        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }

    public function CreditAccountLedger($id){
        try {
            $comp = '';
            if (is_numeric($id)) {
                $comp = DB::table('company')->where("company.systemid",$id)->first();
            }

            if (is_numeric($id) && !empty($comp)) {
                $creditact = DB::table('creditact')->
                join("creditact_ledger", "creditact.id", "creditact_ledger.creditact_id")->
                leftJoin("fuel_receipt", "fuel_receipt.id", "creditact_ledger.document_no")->
                join("company","company.id", "creditact.company_id")->
                where("company.systemid",$id)->
                orderBy('creditact_ledger.id', 'desc')->
				select('creditact.id as creditactId','creditact.amount as totalAmount', 'creditact_ledger.*', 'creditact_ledger.document_no as fuel_receipt_id','fuel_receipt.voided_at as voided', 'fuel_receipt.systemid as document_number')->get();
            }else{
                $creditact = DB::table('creditact')->
                join("creditact_ledger", "creditact.id", "creditact_ledger.creditact_id")->
                leftJoin("fuel_receipt", "fuel_receipt.id", "creditact_ledger.document_no")->
                join("oneway","oneway.id", "creditact.company_id")->
                where("oneway.company_name",$id)->
                orderBy('creditact_ledger.id', 'desc')->
                select('creditact.id as creditactId', 'creditact.amount as totalAmount', 'creditact_ledger.*', 'creditact_ledger.document_no as fuel_receipt_id','fuel_receipt.voided_at as voided', 'fuel_receipt.systemid as document_number')->get();
            }
            $data =array();

            foreach ($creditact as $item) {
                $date = date_create($item->created_at);
                array_push($data, [
                    "creditact_id"=>$item->creditactId,
                    "date" => date_format($date,'dMy H:i:s'),
                    "amount" => number_format(($item->amount/100),2),
                    "sysid" => $item->document_number,
                    "receipt_id" => $item->fuel_receipt_id
                ]);
            }

            Log::debug('credit_account_ledger: creaditact='.
				json_encode($creditact));
            Log::debug('credit_account_ledger: data='.
				json_encode($data));

        return view('credit_ac.credit_account_ledger',
			compact('data', 'creditact', 'id'));

        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File" => $e->getFile(),
                "Line" => $e->getLine()
            ]);
            abort(404);
        }
    }

    public function CreditAccountLedgerDatatable(Request $request){
        try {
            $id = $request->id;

            $comp = '';
            if (is_numeric($id)) {
                $comp = DB::table('company')->
					where("company.systemid",$id)->first();
            }

            if (is_numeric($id) && !empty($comp)) {
                $creditact = DB::table('creditact')->
                join("creditact_ledger", "creditact.id", "creditact_ledger.creditact_id")->
                // leftJoin("fuel_receipt", "fuel_receipt.id", "creditact_ledger.document_no")->
                join("company","company.id", "creditact.company_id")->
                where("company.systemid",$id)->
                orderBy('creditact_ledger.last_update', 'desc')->
				select(
					'creditact.id as creditactId',
					'creditact.amount as totalAmount',
					'creditact.status as status',
					'creditact_ledger.*',
					'creditact_ledger.document_no as fuel_receipt_id',
					// 'fuel_receipt.voided_at as voided',
					// 'fuel_receipt.status as status',
					// 'fuel_receipt.systemid as document_number',
                  'creditact_ledger.source as source'
				)->get();

            }else{
                $creditact = DB::table('creditact')->
                join("creditact_ledger", "creditact.id", "creditact_ledger.creditact_id")->
                // leftJoin("fuel_receipt", "fuel_receipt.id", "creditact_ledger.document_no")->
                join("oneway","oneway.id", "creditact.company_id")->
                where("oneway.company_name",$id)->
                orderBy('creditact_ledger.last_update', 'desc')->
                select(
                  'creditact.id as creditactId',
                  'creditact.amount as totalAmount',
                  'creditact.status as status',
                  'creditact_ledger.*',
                  'creditact_ledger.document_no as fuel_receipt_id',
                  // 'fuel_receipt.voided_at as voided',
                  // 'fuel_receipt.status as status',
                  // 'fuel_receipt.systemid as document_number',
                  // 'fuel_receipt.id as fuelId',
                  'creditact_ledger.source as source'
                  )->get();
            }
            $data =array();

            foreach ($creditact as $item) {

                $date = date_create($item->created_at);

                switch ($item->source) {
				case "fuel":
                    $fuel = DB::table('fuel_receipt')->
						join("fuel_receiptlist","fuel_receiptlist.fuel_receipt_id", "fuel_receipt.id")->
						join("fuel_receiptdetails","fuel_receiptdetails.receipt_id", "fuel_receipt.id")->
						where("fuel_receipt.id", "=", $item->fuel_receipt_id)->
						select(
							'fuel_receipt.voided_at as voided_at',
							'fuel_receipt.status as status',
							'fuel_receipt.systemid as systemid',
							'fuel_receipt.id as fuelId',
							'fuel_receiptlist.newsales_item_amount',
							'fuel_receiptlist.newsales_rounding',
							'fuel_receiptlist.newsales_tax',
						)->first();
						$item->sysid = $fuel->systemid;
						$item->voided = $fuel->voided_at;
						$item->status = $fuel->status;
						break;

				case "fulltank":
                    $full_tank = DB::table('fuelfulltank_receipt')->
						join("fuelfulltank_receiptlist","fuelfulltank_receiptlist.fuel_fulltank_receipt_id", "fuelfulltank_receipt.id")->
						join("fuelfulltank_receiptdetails","fuelfulltank_receiptdetails.fulltank_receipt_id", "fuelfulltank_receipt.id")->
						where("fuelfulltank_receipt.id", "=", $item->fuel_receipt_id)->
						select(
							'fuelfulltank_receipt.voided_at as voided_at',
							'fuelfulltank_receipt.status as status',
							'fuelfulltank_receipt.systemid as systemid',
							'fuelfulltank_receipt.id as fuelId',
						)->first();
                    $item->sysid = $full_tank->systemid;
                    $item->voided = $full_tank->voided_at;
                    $item->status = $full_tank->status;
                    break;

				default:
					if ($item->source == 'payment') {
						$item->sysid = 'Payment';
					} else {
						$item->sysid = $item->fuel_receipt_id;
					}
                    $item->voided = null;
                }

                if ($item->status == 'refunded') {
                  $fuel_refunded = DB::table('fuel_receipt')->
				  	join("fuel_receiptlist","fuel_receiptlist.fuel_receipt_id", "fuel_receipt.id")->
					join("fuel_receiptdetails","fuel_receiptdetails.receipt_id", "fuel_receipt.id")->
					where("fuel_receipt.id", "=", $item->fuel_receipt_id)->
					select(
						'fuel_receipt.voided_at as voided_at',
						'fuel_receipt.status as status',
						'fuel_receipt.systemid as systemid',
						'fuel_receipt.id as fuelId',
						'fuel_receiptlist.total',
						'fuel_receiptlist.newsales_item_amount',
						'fuel_receiptlist.newsales_rounding',
						'fuel_receiptlist.newsales_tax',
					)->first();

                    $refunded_amount = $fuel_refunded->total -
						($fuel_refunded->newsales_item_amount +
						$fuel_refunded->newsales_rounding +
						$fuel_refunded->newsales_tax);


					$c_count = DB::table('creditact_ledger')->
						where('document_no',$item->sysid)->count();


					if(!$c_count) {
						 DB::table('creditact_ledger')->
						 insert([
							'document_no'=> $item->sysid,
							'creditact_id'=>$item->creditactId,
							'source'=> $item->status,
							'amount'=>-$refunded_amount,
							'last_update' => date('Y-m-d H:i:s'),
							'created_at' => date('Y-m-d H:i:s'),
							'updated_at' => date('Y-m-d H:i:s'),
						]);
					}

				} 

				if ($item->source == "refunded") {
					$pk = DB::table('fuel_receipt')->
						where('systemid', $item->fuel_receipt_id)->
						select('id')->
						first();

					//Log::info('WS1 refunded item='.json_encode($item));
					//Log::info('WS1 refunded pk='.json_encode($pk));
					$item->source = "fuel";
					$item->fuel_receipt_id = $pk->id;
					$refunded = true;

				} else {
					$refunded = false;
				}

				array_push($data, [
					"creditact_id"=>$item->creditactId,
					"date" => date_format($date,'dMy H:i:s'),
					"amount" => number_format($item->amount/100,2),
					"sysid" => $item->sysid,
					"receipt_id" => $item->fuel_receipt_id,
					"voided" => $item->voided,
					"status" => $item->status,
					"refunded" => $refunded,
					"source" => $item->source,
				]);
            }

			// Have to filter off: sysid=='Payment' and source=='refunded'
            Log::debug('creditact_ledger: data='.json_encode($data));

            return DataTables::of($data)->
				addIndexColumn()->make(true);

        } catch (\Exception $e) {
            Log::error([
                "Error" => $e->getMessage(),
                "File"  => $e->getFile(),
                "Line"  => $e->getLine()
            ]);
            abort(404);
        }
    }


    public function newPayment(Request $request)
    {
        $user_connected_id = Auth::user()->id;
        log::debug("**** RAC() ****");
        Log::debug("request->creditactId=".$request->creditact_id);

        $creditact = DB::table('creditact')->
			whereId($request->creditact_id)->first();

		if (empty($creditact)) {
			/* BREAK DOWN URL TO GET COMPANY NAME*/
			$url = parse_url($request->src_url);
			$v3 = explode("/", $url['path']);
			$co_name = urldecode(end($v3));

			Log::info('url='.json_encode($url));
			Log::info('v3='.json_encode($v3));
			Log::info('co_name='.$co_name);

			$oneway_info = DB::table('oneway')->
				where('company_name', $co_name)->first();

			$creditact = null;
			if (!empty($oneway_info)) {
				$creditact = DB::table('creditact')->
					where('company_id', $oneway_info->id)->first();
			}

			if (empty($creditact)) {
				$account_id = DB::table('creditact')->
					insertGetId([
					"company_id" => $oneway_info->id,
					"fuel_receipt_id" => NULL,
					"amount" => 0,
					"status" => 'active',
					"deleted_at" => NULL,
					"created_at" => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s')
				]);
            $creditact_amount = 0;

			} else {
				$account_id = $creditact->id;
				$creditact_amount = $creditact->amount;
			}

        } else {
			$creditact_amount = $creditact->amount;
			$account_id = $request->creditact_id;
        }

        $newAmount = $creditact_amount - $request->value;
       if ($request->value > 0) 
	   {
        DB::table('creditact')->
            where('id', $account_id)
              ->update([
				'amount'=>$newAmount,
				'updated_at' => date('Y-m-d H:i:s')
			  ]);
		 }
		 else
		 {
		  $response = "enter valid amount";
		  return $response;
		}
	
        if($request->value > 0){
        DB::table('creditact_ledger')
			->insert([
				'document_no'=>'Payment',
				'creditact_id'=>$account_id,
				'source'=>$request->source,
				'amount'=>-$request->value,
				'last_update' => date('Y-m-d H:i:s'),
				'created_at' => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s'),
			]);
	    

        return ["message" => "CreditAccount: Fund updated successfully",
            "error" => false];
		}
    }

    public static function getUserCompany($user_id)
    {
        $company = Company::where("owner_user_id", $user_id)->first();
        return $company;
    }


    function deleteMerchantLinkWithRelation(Request $request)
    {
        try {
            MerchantLink::find($request->id)->delete();
            MerchantLinkRelation::where("merchantlink_id",
				$request->id)->delete();

            return ["message" => "deleting done", "error" => false];
        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => true];
        }
    }


    function oneWayDeleteMerchantLinkWithRelation(Request $request)
    {
        try {
           // Log::info($request->all());
            Oneway::find($request->companyId)->delete();
            Onewayrelation::where('oneway_id',
				$request->companyId)->delete();

            return ["message" => "deleting done", "error" => false];
        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => true];
        }
    }


    function editMerchantLinkRelation(Request $request)
    {
        try {
			Log::info($request->all());
			Log::info("it goes there");
			MerchantLinkRelation::where("merchantlink_id",
				$request->merchantLinkId)->
			where("ptype", $request->ptype)->
			update(["status" => $request->status]);

            return ["message" => "update done", "error" => false];

        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => true];
        }
    }


    function  getAllData(){
        $user_connected_id = Auth::user()->id;

		Log::debug('getAllData: user_connected_id='.
			json_encode($user_connected_id));

        $merchantLink = DB::table('merchantlink')->
			join('company', function ($join) {
				$user_connected_id = Auth::user()->id;
				$join->on('merchantlink.responder_user_id', '=',
					'company.owner_user_id')->
                    whereNull('company.deleted_at')->
				where('company.owner_user_id','!=',$user_connected_id)->
				orOn('merchantlink.initiator_user_id', '=',
					'company.owner_user_id');
			})->
			join('users', function ($join) {
				$join->on('merchantlink.responder_user_id', '=', 'users.id')->
				orOn('merchantlink.initiator_user_id', '=', 'users.id');
			})->
			join("merchantlinkrelation", "merchantlinkrelation.merchantlink_id",
				"merchantlink.id")->
				select('company.id','company.name','company.systemid','company.status',
					'company.credit_limit','company.corporate_logo')->
			groupBy('company.id','company.name','company.systemid',
				'company.credit_limit','company.status','company.corporate_logo')->
			get();

		Log::debug('getAllData: merchantLink='.
			json_encode($merchantLink));

        $company = self::getUserCompany($user_connected_id);
        $onewayrelations = DB::table('oneway')->whereNull('deleted_at')->get();

        $data =array();

        foreach ($merchantLink as $item) {
            $status = $item->status == null ? "Active" : $item->status;
            $total = DB::table('creditact')->
				where('company_id',$item->id)->
				select(DB::raw('SUM(amount) as total'))->
				get()[0];

            array_push($data, [
				"name_company" => $item->name ,
				"status" => $status,
				"sysid" => $item->systemid,
				"type"=>"twoway",
                "credit_limit" => $item->credit_limit,
				"amount" => number_format($total->total/100,2),
				"company_id" => $item->id
			]);
        }


        foreach ($onewayrelations as $d) {
            Log::info("onewayrelations d=".json_encode($d));
            $total = DB::table('creditact')->
            where('company_id',$d->id)->
            select(DB::raw('SUM(amount) as total'))->
            get()[0];

            array_push($data, [
				"name_company" => $d->company_name ,
				"status" => $d->status,
				"sysid" => "-",
				"type"=>"oneway",
                //"credit_limit" => number_format($d->credit_limit/100,2),
                "credit_limit" => $d->credit_limit,
				"amount" => number_format($total->total/100,2),
				"company_id" => $d->id
			]);
        }

		Log::debug('getAllData: data'.json_encode($data));

        return $data;
    }


    function creditAccountList()
    {
        try {
            $data = $this->getAllData();

            $data_minus_refund = [];
            foreach ($data as $creditac) {

              $creditac['refunds'] = 0;

              $ca_data = DB::table('creditact')->
                            where("company_id", "=", $creditac['company_id'])->
                            whereNull('deleted_at')->get();

              if (!empty($ca_data[0])) {
                $ledger = DB::table('creditact_ledger')->where("creditact_id", "=", $ca_data[0]->id)
                            ->select('id', 'document_no', 'source', )
                            ->get();

                foreach ($ledger as $transaction) {
                  switch ($transaction->source) {
                    case "fuel":

                      $record = DB::table('fuel_receipt')
						  ->join("fuel_receiptlist","fuel_receiptlist.fuel_receipt_id", "fuel_receipt.id")
						  ->where('fuel_receipt.id', '=', $transaction->document_no)
						  ->select(
							  'fuel_receipt.status',
							  'fuel_receiptlist.newsales_item_amount',
							  'fuel_receiptlist.newsales_rounding',
							  'fuel_receiptlist.newsales_tax',
							  'fuel_receiptlist.total',
							)
						  ->first();

                      if ($record->status == 'refunded') {
                        $creditac['refunds'] += $record->total - ($record->newsales_item_amount + $record->newsales_rounding + $record->newsales_tax);
                      }
                      break;
                    case "fulltank":
                      break;
                  }
                }
              }

              $creditac['amount'] = str_replace( ',', '', $creditac['amount']);
              $creditac['amount'] = ($creditac['amount'] * 100) - $creditac['refunds'] ;
              $creditac['amount'] = number_format(($creditac['amount']/100),2);

              $has_ledger = DB::table('creditact')->
                            where("company_id", "=", $creditac['company_id'])->
                            first();

              if (empty($has_ledger->id)) {
                  $creditac['has_legder_account'] = "false";
              } else {
                  $creditac['has_legder_account'] = "true";
              }

              $data_minus_refund[] = $creditac;
            }

            Log::debug('AccountList:'.json_encode($data_minus_refund));

            return DataTables::of($data_minus_refund)->
				addIndexColumn()->make(true);

        } catch (\Exception $e) {
          Log::error([
              "Error" => $e->getMessage(),
              "File" => $e->getFile(),
              "Line" => $e->getLine()
          ]);
          abort(404);
            return ["message" => $e->getMessage(), "error" => false];
        }
    }


    function listMerchantActive()
    {
        try {
            $finalListMerchant =  $data = $this->getAllData();

			Log::debug('listMerchantActive;'.json_encode($data));

            return ["data" => $finalListMerchant, "error" => false];

        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }


    function creditAccountListLedger($systemid)
    {
        try {

            $data = $this->CreditAccountLedger();

            return DataTables::of($data)->
				addIndexColumn()->make(true);


        } catch (\Exception $e) {
            return ["message" => $e->getMessage(), "error" => false];
        }
    }


    function changeOnewayRelationStatus(Request $request){
        Log::info($request->all());
        $onewayRelation = Onewayrelation::where("oneway_id",
			$request->input('merchantLinkRelationId'))->first();

        $onewayRelation->status = $request->input('status');
        $onewayRelation->save();
        return response()->json([
			'msg' => 'Status updated successfully',
			'status' => 'true'
		]);
    }

    function saveMerchandLink(Request $request){
		Log::info('**** saveMerchandLink() ****');
		Log::info('request->user_id='.$request->user_id);
		Log::info('request->url='.$request->url);
		Log::info('request->companyId='.$request->companyId);

        $item = json_decode($request->mlink);
        $user_connected_id = $request->user_id;

		$dealer = null;
		if ($item->initiator_user_id == $user_connected_id) {
			$dealer = $item->responder_user;
		} else {
			$dealer = $item->initiator_user;
		}

		if (!User::whereEmail($dealer->email)->first()) {
			$systemid = SystemID::openitem_system_id(1);
			User::create([
				"systemid" => $systemid,
				"id" => $dealer->id,
				"email" => $dealer->email,
				"type" => $dealer->type,
				"status" => $dealer->status,
			]);
		}

		Log::info('dealer->user_company='.
			json_encode($dealer->user_company));

		//$crec = DB::table('company')->
		$crec = Company::where('id',$dealer->user_company->id)->get();

		$duc = $dealer->user_company;

		Log::debug('duc->id='.$duc->id);
		Log::debug('crec='.json_encode($crec));
		Log::debug('crec->count()='.$crec->count());

		if ($crec->count() == 0) {
			Log::debug('**** company_id='.$duc->id.' DOES NOT exist!!');

			try{
				//DB::table('company')->insert(
				Company::insert(
				[
					'id' => $duc->id,
					'systemid' => $duc->systemid,
					'name' => $duc->name,
					'business_reg_no' => $duc->business_reg_no,
					'corporate_logo' => $duc->corporate_logo,
					'owner_user_id' => $duc->owner_user_id,
					'gst_vat_sst' => $duc->gst_vat_sst,
					'currency_id' => $duc->currency_id,
					'office_address' => $duc->office_address,
					'status' => $duc->status,
				]);
			}catch (QueryException $e){
				$errorCode = $e->errorInfo[1];
				if($errorCode == 1062){
					Log::error($request->companyId." company already exist");
				}
			}
		}

		$mlrec = DB::table('merchantlink')->
			where('id',$item->id)->get();
		Log::debug('2. mlrec='.json_encode($mlrec));

		if (empty($mlrec)) {
			Log::debug('**** JUST BEFORE inserting into merchantlink ****');
			DB::table('merchantlink')->insert(
				[
					"id" => $item->id,
					"initiator_user_id" => $item->initiator_user_id,
					"responder_user_id" => $item->responder_user_id,
					"status" => $item->status
				]
			);
		}

		for ($m = 0; $m < sizeof($item->merchant_link_relation); $m++) {
			$mlr = $item->merchant_link_relation[$m];
			if (MerchantLinkRelation::whereId($mlr->id)->count() == 0) {
				DB::table('merchantlinkrelation')->insert(
					[
						"id" => $mlr->id,
						"company_id" => $mlr->company_id,
						"merchantlink_id" => $mlr->merchantlink_id,
						"default_location_id" => $mlr->default_location_id,
						"ptype" => $mlr->ptype,
						"status" => $mlr->status,
					]
				);
			}
		}

        return response()->json([
			'msg' => 'Save successfully',
			'status' => 'true'
		]);
    }

	public function download_ca_pdf_stmt(Request $request) {
		try {
			Log::debug('Start Date: ' . $request->ca_start_date);
			Log::debug('Stop Date: ' . $request->ca_end_date);
			Log::debug('Company: ' . $request->company);
			Log::debug('Company ID: ' . $request->company_id);
			Log::debug('Company Type: ' . $request->type);

			if ($request->type == 'oneway') {
				$company = DB::table('oneway')->
					whereId($request->company_id)->
					whereNull('deleted_at')->
					first();
				$company->account_name = $company->company_name;

			} else {
				$company = DB::table('company')->
					whereId($request->company_id)->
					whereNull('deleted_at')->
					first();
				$company->account_name = $company->name;
			}

			$dateS = Carbon::parse($request->ca_start_date);
			$dateE = Carbon::parse($request->ca_end_date);

			$creditact = DB::table('creditact')->
				where('company_id',$request->company_id)->
				first();

			$total = 0;
			$company->balance = 0;

			if (!empty($creditact)) {
				$total = DB::table('creditact_ledger')->
					where('creditact_id',$creditact->id)->
					whereBetween(
						  'created_at',
						  [
							  $dateS->format('Y-m-d')." 00:00:00",
							  $dateE->format('Y-m-d')." 23:59:59"
						  ]
						)->
					select(DB::raw('SUM(amount) as total'))->
					get()[0];

				Log::debug('download_ca_pdf_stmt: creditact->id='.
					$creditact->id);
				Log::debug('download_ca_pdf_stmt: dateS='.
					json_encode($dateS));
				Log::debug('download_ca_pdf_stmt: dateE='.
					json_encode($dateE));
				Log::debug('download_ca_pdf_stmt: total='.
					json_encode($total));

				// This is wrong!! Because refund is not deducted!!
				$company->balance = $total->total;
			}

			$company->stmt_start_date = $request->ca_start_date;
			$company->stmt_end_date = $request->ca_end_date;

			if ($request->type == 'twoway') {
				$creditact = DB::table('creditact')->
				join("creditact_ledger", "creditact.id", "creditact_ledger.creditact_id")->
				join("company","company.id", "creditact.company_id")->
				where("company.id",$request->company_id)->
				whereBetween(
					'creditact_ledger.created_at',
					[
						$dateS->format('Y-m-d')." 00:00:00",
						$dateE->format('Y-m-d')." 23:59:59"
					]
				)->
				orderBy('creditact_ledger.last_update', 'desc')->
				select(
					'creditact.id as creditactId',
					'creditact.amount as totalAmount',
					'creditact.status as status',
					'creditact_ledger.*',
					'creditact_ledger.document_no as fuel_receipt_id',
					'creditact_ledger.source as source'
				)->get();

			}else{
				$creditact = DB::table('creditact')->
				join("creditact_ledger", "creditact.id", "creditact_ledger.creditact_id")->
				join("oneway","oneway.id", "creditact.company_id")->
				where("oneway.id",$request->company_id)->
				whereBetween(
					'creditact_ledger.created_at',
					[
						$dateS->format('Y-m-d')." 00:00:00",
						$dateE->format('Y-m-d')." 23:59:59"
					]
				)->
				orderBy('creditact_ledger.last_update', 'desc')->
				select(
					'creditact.id as creditactId',
					'creditact.amount as totalAmount',
					'creditact.status as status',
					'creditact_ledger.*',
					'creditact_ledger.document_no as fuel_receipt_id',
					// 'fuel_receipt.voided_at as voided',
					// 'fuel_receipt.status as status',
					// 'fuel_receipt.systemid as document_number',
					// 'fuel_receipt.id as fuelId',
					'creditact_ledger.source as source'
				)->get();
			}

			$data =array();
			$balance = 0;

			foreach ($creditact as $item) {
				$date = date_create($item->created_at);

				switch ($item->source) {
					case "fuel":
						$fuel = DB::table('fuel_receipt')->
      						join("fuel_receiptlist","fuel_receiptlist.fuel_receipt_id", "fuel_receipt.id")->
      						join("fuel_receiptdetails","fuel_receiptdetails.receipt_id", "fuel_receipt.id")->
      						where("fuel_receipt.id", "=", $item->fuel_receipt_id)->
                            whereBetween(
                                  'fuel_receipt.created_at',
                                  [
                                      $dateS->format('Y-m-d')." 00:00:00",
                                      $dateE->format('Y-m-d')." 23:59:59"
                                  ]
                                )->
      						select(
      							'fuel_receipt.voided_at as voided_at',
      							'fuel_receipt.status as status',
      							'fuel_receipt.systemid as systemid',
      							'fuel_receipt.id as fuelId',
      							'fuel_receiptlist.newsales_item_amount',
      							'fuel_receiptlist.newsales_rounding',
      							'fuel_receiptlist.newsales_tax',
      						)->first();

						$item->sysid = '';
						$item->voided = '';
						$item->status = '';

						if (!empty($fuel)) {
							$item->sysid = $fuel->systemid;
							$item->voided = $fuel->voided_at;
							$item->status = $fuel->status;
						}

						break;

					case "fulltank":
						$full_tank = DB::table('fuelfulltank_receipt')->
      						join("fuelfulltank_receiptlist","fuelfulltank_receiptlist.fuel_fulltank_receipt_id", "fuelfulltank_receipt.id")->
      						join("fuelfulltank_receiptdetails","fuelfulltank_receiptdetails.fulltank_receipt_id", "fuelfulltank_receipt.id")->
      						where("fuelfulltank_receipt.id", "=", $item->fuel_receipt_id)->
                            whereBetween(
                                  'fuelfulltank_receipt.created_at',
                                  [
                                      $dateS->format('Y-m-d')." 00:00:00",
                                      $dateE->format('Y-m-d')." 23:59:59"
                                  ]
                                )->
      						select(
      							'fuelfulltank_receipt.voided_at as voided_at',
      							'fuelfulltank_receipt.status as status',
      							'fuelfulltank_receipt.systemid as systemid',
      							'fuelfulltank_receipt.id as fuelId',
      						)->first();

						$item->sysid = '';
						$item->voided = '';
						$item->status = '';

						if (!empty($full_tank)) {
							  $item->sysid = $full_tank->systemid;
							  $item->voided = $full_tank->voided_at;
							  $item->status = $full_tank->status;
						}
						break;

      				default:
                          $item->sysid = 'Payment';
                          $item->voided = null;
				}

				if ($item->status == 'refunded') {
					$fuel_refunded = DB::table('fuel_receipt')->
					join("fuel_receiptlist","fuel_receiptlist.fuel_receipt_id", "fuel_receipt.id")->
					join("fuel_receiptdetails","fuel_receiptdetails.receipt_id", "fuel_receipt.id")->
					where("fuel_receipt.id", "=", $item->fuel_receipt_id)->
					whereBetween(
						'fuel_receipt.created_at',
						[
							$dateS->format('Y-m-d')." 00:00:00",
							$dateE->format('Y-m-d')." 23:59:59"
						]
					)->select(
						'fuel_receipt.voided_at as voided_at',
						'fuel_receipt.status as status',
						'fuel_receipt.systemid as systemid',
						'fuel_receipt.id as fuelId',
						'fuel_receiptlist.total',
						'fuel_receiptlist.newsales_item_amount',
						'fuel_receiptlist.newsales_rounding',
						'fuel_receiptlist.newsales_tax',
					)->first();

					$refunded_amount = 0;

					if (!empty($fuel_refunded)) {
						$refunded_amount = $fuel_refunded->total -
						  ($fuel_refunded->newsales_item_amount +
						  $fuel_refunded->newsales_rounding +
						  $fuel_refunded->newsales_tax);
					}

					Log::debug('download_ca_pdf_stmt: refunded_amount='.
						$refunded_amount);

					$balance = $balance - $refunded_amount;

					array_push($data, [
						"creditact_id"=>$item->creditactId,
						"date" => date_format($date,'dMy H:i:s'),
						"amount" => "-".(number_format($refunded_amount/100,2)),
						"sysid" => $item->sysid,
						"receipt_id" => $item->fuel_receipt_id,
						"voided" => $item->voided,
						"status" => $item->status,
						"refunded" => true,
						"source" => $item->source,
					]);
				}

				$balance = $balance + $item->amount;

				Log::debug('download_ca_pdf_stmt: amount='. $item->amount);
				Log::debug('download_ca_pdf_stmt: balance='. $balance);

				/*
				Log::debug('download_ca_pdf_stmt: 1. data='.
					json_encode($data));
				*/

				array_push($data, [
					"creditact_id"=>$item->creditactId,
					"date" => date_format($date,'dMy H:i:s'),
					"amount" => number_format($item->amount/100,2),
					"sysid" => $item->sysid,
					"receipt_id" => $item->fuel_receipt_id,
					"voided" => $item->voided,
					"status" => $item->status,
					"source" => $item->source,
				]);


				/*
				Log::debug('download_ca_pdf_stmt: 2. data='.
					json_encode($data));
				*/
			}


			$company->balance = $balance;

			$transactions = $data;


			/*
			Log::debug('download_ca_pdf_stmt: 3. data='.
				json_encode($data));
			*/


			$pdf_name = $company->account_name . '-CAStatement-'.
				$request->ca_start_date . '-'.$request->ca_end_date . '.pdf';

			Log::debug([
				'Company DB: ' => $company
			]);

			$pdf = PDF::setOptions(
				[
				  'isHtml5ParserEnabled' => true,
				  'isRemoteEnabled' => true
				]
				)->loadView(
					'printing.creditac_stmt_pdf',
					compact(
						'transactions',
						'company'
					)
				);

			$pdf->getDomPDF()->setBasePath(public_path() . '/');
			$pdf->getDomPDF()->setHttpContext(
				stream_context_create([
					'ssl' => [
					  'allow_self_signed' => true,
					  'verify_peer' => false,
					  'verify_peer_name' => false,
					],
				])
			);
			$pdf->setPaper('A4', 'portrait');
			return $pdf->download($pdf_name);

		} catch (\Exception $e) {
            Log::error([
                'Message' => $e->getMessage(),
                'File' => $e->getFile(),
                'Line' => $e->getLine(),
            ]);
		}
    }

	public function save_merchant(Request $request) {
		$system_id= new \App\Classes\SystemID('creditact');
		$system_id->__toString();
		
		try {
			$merchant_record = DB::table('oneway')->
				where("company_name", $request->merchant_name)->
				whereNull("deleted_at")->
				first();

		if (empty($merchant_record)) {
		  $merchant_id = DB::table('oneway')->
			  insert([
			  "self_merchant_id" => '15',
			  "company_name" => $request->merchant_name,
			  "systemid" => $system_id,
			  "status" => 'active',
			  "deleted_at" => NULL,
			  "created_at" => date('Y-m-d H:i:s'),
			  'updated_at' => date('Y-m-d H:i:s'),
		  ]);
		}
	  } catch (\Exception $e) {
		Log::error([
			'Message' => $e->getMessage(),
			'File' => $e->getFile(),
			'Line' => $e->getLine(),
		]);
	  }
	}


public function save_credit_limit(Request $request) {
  try {

      if ($request->is_merchant == 'true') {
          // In oneway
          $merchant = DB::table('oneway')->
                          where('company_name', 'like', '%' . str_replace('_', ' ', $request->merchant) . '%')->
                          first();
          $merchant = DB::table('oneway')->
          whereId($merchant->id)->
          update([
              "credit_limit" => $request->credit_limit,
              'updated_at' => date('Y-m-d H:i:s'),
          ]);
      } else {
          // in company
          $merchant = DB::table('company')->
          where('systemid', '=', $request->merchant)->
          update([
              "credit_limit" => $request->credit_limit,
              'updated_at' => date('Y-m-d H:i:s'),
          ]);
      }

  } catch (\Exception $e) {
    Log::error([
        'Message' => $e->getMessage(),
        'File' => $e->getFile(),
        'Line' => $e->getLine(),
    ]);
  }


}

public function check_merchant_account(Request $request) {
    try {
        $merchant = '';
        if ($request->is_merchant == 'true') {

            // In oneway
            $merchant = DB::table('oneway')->
                            where('company_name', 'like', '%' . str_replace('_', ' ', $request->merchant) . '%')->
                            first();

        } else {
            // in company
            $merchant = DB::table('company')->
                            where('systemid', '=', $request->merchant)->
                            first();
        }

        if (!empty($merchant)) {
            $merchant_creditact = DB::table('creditact')->
                where('company_id', $merchant->id)->first();

            if (!empty($merchant_creditact)) {
                $merchant_creditact_ledger = DB::table('creditact_ledger')->
                    where('creditact_id', $merchant_creditact->id)->first();
            }

            if (!empty($merchant_creditact) || !empty($merchant_creditact_ledger)) {
                return response()->json([
                    'account_exists' => true,
                ]);
            }
        }
        return response()->json([
            'account_exists' => false,
        ]);

    } catch (\Exception $e) {
      Log::error([
          'Message' => $e->getMessage(),
          'File' => $e->getFile(),
          'Line' => $e->getLine(),
      ]);
    }
}

public function delete_merchant(Request $request) {
  try {
      if((strpos('_', $request->merchant) !== false) || (preg_match("/[a-zA-Z]/i", $request->merchant))){
            // In oneway
            $merchant = DB::table('oneway')->
                where('company_name', 'like', '%' . str_replace('_', ' ', $request->merchant) . '%')->
                whereNull('deleted_at')->
                first();
        } else{
            // in company
            $merchant = DB::table('company')->
                where('systemid', $request->merchant)->
                whereNull('deleted_at')->
                first();
        }

        $merchant_ledger_records = DB::table('creditact_ledger')->
                        where('creditact_id', $merchant->id)->
                        first();

        if (empty($merchant_ledger_records)) {
            $merchant = DB::table('oneway')->
            whereId($merchant->id)->
            update([
                'deleted_at' => date('Y-m-d H:i:s'),
            ]);
        }

  } catch (\Exception $e) {
    Log::error([
        'Message' => $e->getMessage(),
        'File' => $e->getFile(),
        'Line' => $e->getLine(),
    ]);
  }
}

    function saveMerchandLinkOneWay(Request $request){

        Log::debug("**** saveMerchandLinkOneWay() ****");
        Log::debug($request->all());
        Log::debug("request->companyId=".$request->companyId);

        $item = json_decode($request->mlink);
        $onewayrelation = $item->onewayrelation;

        $user_connected_id = $request->user_id;
        $companyId = $request->companyId;
        $oneway = $item;

        //Log::info($oneway);
        if (empty($companyId)) {
            if (DB::table('oneway')->
				where('id' , $oneway->id)->count() == 0) {
                $set = [
                    "id" => $oneway->id,
                    "self_merchant_id" => $oneway->self_merchant_id,
                    "company_name" => $oneway->company_name,
                    "business_reg_no" => $oneway->business_reg_no,
                    "address" => $oneway->address,
                    "contact_name" => $oneway->contact_name,
                    "mobile_no" => $oneway->mobile_no,
                    "status" => $oneway->status,
                ];

                 DB::table('oneway')->insertGetId($set);

                Log::debug("Data ready to inserted: request->companyId=".
					$request->companyId);

                $onewayrelation = $item->onewayrelation;
                if (Onewayrelation::whereId($onewayrelation->id)->count() == 0) {
                    Onewayrelation::create([
                        "oneway_id" => $onewayrelation->oneway_id,
                        "default_location_id" => $onewayrelation->default_location_id,
                        "ptype" => $onewayrelation->ptype,
                        "status" => $onewayrelation->status,
                    ]);
                }

                $onewaylocation = $item->onewaylocation;
                if (Onewaylocation::whereId($onewaylocation->id)->count() == 0) {
                    Onewaylocation::create([
                        "oneway_id" => $onewaylocation->oneway_id,
                        "location_id" => $onewaylocation->location_id,
                        "deleted_at" => $onewaylocation->deleted_at
                    ]);
                }
            }

        }else{
			Log::debug("Company is empty: request->companyId=".
				$request->companyId);

            Oneway::whereId($companyId)->update([
                "self_merchant_id" => $oneway->self_merchant_id,
                "company_name" => $oneway->company_name,
                "business_reg_no" => $oneway->business_reg_no,
                "address" => $oneway->address,
                "contact_name" => $oneway->contact_name,
                "mobile_no" => $oneway->mobile_no,
                "status" => $oneway->status,
            ]);
        }

        return response()->json([
			'msg' => 'Save successfully',
			'status' => 'true'
		]);
    }


    function  getLedgerData($systemid){
        $creditact = DB::table('creditact')->
			join("fuel_receipt", "fuel_receipt.id", "creditact.fuel_receipt_id")->
			join("company","company.id", "creditact.company_id")->
			where("company.systemid",$systemid)->
			select('creditact.*', 'fuel_receipt.systemid as document_number')->get();


        $data =array();

        foreach ($creditact as $item) {
			$date = date_create($item->created_at);
            array_push($data, [
				"date" => date_format($date,'dMy H:i:s'),
				"amount" => number_format($item->amount,2),
				"sysid" => $item->document_number,
				"receipt_id" => $item->fuel_receipt_id
			]);
        }

        return $data;
    }


    // function receiptCreditAction(Request $request){
    //     $user_connected_id = Auth::user()->id;
    //     log::debug("**** RAC() ****");
    //     Log::debug("request->companyId=".$request->companyId);
    //     Log::debug("request->companyId=".$request->receipt_id);
    //     Log::debug("request->companyId=".$request->credit_ac);

    //     $receiptCount = DB::table('creditact')->
	// 		where('fuel_receipt_id', $request->receipt_id)->
	// 		get()->count();

    //     if ($receiptCount == 0) {
    //     DB::table('creditact')->
	// 		insert(['fuel_receipt_id'=>$request->receipt_id,
	// 			'amount'=>$request->credit_ac,
	// 			'company_id'=>$request->companyId,
	// 			'status'=>'active',
	// 			"created_at" => date('Y-m-d H:i:s'),
	// 			'updated_at' => date('Y-m-d H:i:s')
	// 		]);
    //     }

    //     return ["message" => "CreditAccount: Fund added successfully",
    //         "error" => false];
    // }

    /*
    function receiptCreditAction(Request $request){
        $user_connected_id = Auth::user()->id;
        log::debug("**** RAC() ****");
        Log::debug("request->companyId=".$request->companyId);
        Log::debug("request->companyId=".$request->receipt_id);
        Log::debug("request->companyId=".$request->credit_ac);

        $receiptCount = DB::table('creditact')->
			where('fuel_receipt_id', $request->receipt_id)->
			get()->count();

        //if ($receiptCount == 0) {
        $creditact_id = DB::table('creditact')->
        insertGetId([//'fuel_receipt_id'=>$request->receipt_id,
				//'amount'=>$request->credit_ac,
				'company_id'=>$request->companyId,
				'status'=>'active',
				"created_at" => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
        //}

        return ["message" => "CreditAccount: Fund added successfully",
            "error" => false, 'creditact_id' => $creditact_id];
    }*/


    //new logic for creitac and creditac_ledger
    function receiptCreditAction(Request $request){
        $user_connected_id = Auth::user()->id;
        log::debug("**** RAC() ****");
        Log::debug("request->companyId=".$request->companyId);
        Log::debug("request->companyId=".$request->receipt_id);
        Log::debug("request->companyId=".$request->credit_ac);

        $receiptCount = DB::table('creditact')->
			where('company_id', $request->companyId)->
			get()->count();

        if ($receiptCount == 0) {
            $creditact_id = DB::table('creditact')->
            insertGetId([//'fuel_receipt_id'=>$request->receipt_id,
				'amount'=>$request->credit_ac*100,
				'company_id'=>$request->companyId,
				'status'=>'active',
				"created_at" => date('Y-m-d H:i:s'),
				'updated_at' => date('Y-m-d H:i:s')
			]);
        } else {
            $creditact_id = DB::table('creditact')->
            where('company_id',$request->companyId)->first()->id;
        }

        return ["message" => "CreditAccount: Fund added successfully",
            "error" => false, 'creditact_id' => $creditact_id];
    }

/*
    function receiptCreditActionUpdate(Request $request){
        $user_connected_id = Auth::user()->id;
        log::debug("**** RAC() ****");
        Log::debug("request->companyId=".$request->receipt_id);
        Log::debug("request->companyId=".$request->credit_ac);

        $receiptCount = DB::table('creditact')->
			where('fuel_receipt_id', $request->receipt_id)->
			get()->count();

        if ($receiptCount == 0) {
        DB::table('creditact')->
            where('id', $request->creditact_id)
              ->update(['fuel_receipt_id'=>$request->receipt_id,
				'amount'=>$request->credit_ac,
				'updated_at' => date('Y-m-d H:i:s')
			]);
        }
        return ["message" => "CreditAccount: Fund updated successfully",
            "error" => false];
    }
    */

    //update with new logic
    function receiptCreditActionUpdate(Request $request){

        $user_connected_id = Auth::user()->id;
        log::debug("**** RAC() ****");
        Log::debug("request->receipt_id=".$request->receipt_id);
        Log::debug("request->credit_ac=".$request->credit_ac);

        $creditact = DB::table('creditact')->whereId($request->creditact_id)->first();

		if (!empty($creditact)) {
			$creditact_value = ($request->credit_ac + $request->rounding)*100;

			$newAmount = $creditact->amount + $creditact_value;
			//if ($receiptCount == 0) {
			DB::table('creditact')->
				where('id', $request->creditact_id)->
				update([
					'amount'=>$newAmount,
					'updated_at' => date('Y-m-d H:i:s')
				]);

			DB::table('creditact_ledger')->
				insert([
					'document_no'=>$request->receipt_id,
					'creditact_id'=>$request->creditact_id,
					'source'=>$request->source,
					'amount'=>$creditact_value,
					'last_update' => date('Y-m-d H:i:s'),
					'created_at' => date('Y-m-d H:i:s'),
					'updated_at' => date('Y-m-d H:i:s'),
				]);
			//}

			$message = "CreditAccount: Record updated successfully";
			$error = false;

		} else {
			$message = "CreditAccount: Record not found";
			$error = true;
		}

		Log::info("receiptCreditActionUpdate: ".$message);

		return ["message" => $message, "error" => $error];
    }
}
