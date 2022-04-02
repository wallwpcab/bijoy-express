<?php

namespace App\Http\Controllers\User\Order;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\User;
use App\Models\DeliveryBoy;
use App\Models\Address;
use App\Models\Merchant;
use App\Models\Order;
use App\Models\ReceiverInformations;

class MerchantParcelListController extends Controller
{
    public function merchantParcelList(Request $request){

        if($request->action == 'table'){
            return $this->allParcelList($request);
        }
        return view('user.order.user-parcel-list');
    }

    public function allParcelList(Request $request){

		$userDetails = Session::get('user');
		
        $tbl = 'parcels.';
        $tbl2 = 'delivery_boy.';

		$start = 0;
		$take = 10;

	    if ($request->start || $request->length != 0 ) {
	        $start = intval($request->start);
	        $take = intval($request->length);
	    }

	    // $dbresult = Order::where($tbl.'id','!=','0');

		$dbresult =  Order::join('merchants', 'parcels.id', '=', 'merchants.id')
		->join('delivery_boy', 'parcels.id', '=', 'delivery_boy.id')
		->select('parcels.*', 'merchants.*', 'delivery_boy.*')->where('merchant_id','=',$userDetails->id)
		->first();

	

       
	    // if($request->getParam('search')['value'] != ""){
	    // 	$columns = ['name', 'slug', 'description', 'price', 'sale_price', 'sku', 'quantity', 'created_at', 'product_type', 'unit'];
	    //     $filterSearch = $request->getParam('search')['value'];
	    //     $dbresult = $dbresult->where(function($q) use ($columns, $filterSearch,$tbl){
	    //         foreach($columns as $column){
	    //             $q->orWhere($tbl.$column, 'LIKE', '%'.$filterSearch.'%');
	    //         }
	    //     });
	    // }

		// $dbresult->orderBy($tbl."id");

		// $recordsTotal = $dbresult->count();
		// $recordsFiltered = $dbresult->count();

		$data = array();
		$i = 0;
		// $result = $dbresult->skip($start)->take($take)->get();


		foreach ($dbresult as $row) {
		
           dd($row);
			$data[$i]["id"] 	        = $row->id;
	         $data[$i]["rname"] 	   = $row->name;
	         $data[$i]["remail"] 	   = $row->email;
	         $data[$i]["rphone"] 	   = $row->phone1;
	         $data[$i]["dname"] 	   = $row->email;
	         $data[$i]["dphone"] 	   = $row->email;
	         $data[$i]["ptype"] 	   = $row->email;
	         $data[$i]["sensitivity"]  = $row->email;
	         $data[$i]["unit"]    	   = $row->email;
	         $data[$i]["payment"] 	   = $row->email;
             $data[$i]["paymethod"]    = $row->email;
	         $data[$i]["fee"]          = $row->email;
		    

		    $i++;
		}

	    $output = array('draw' => $_REQUEST['draw']);

	    $output['data'] = $data;
	    echo json_encode($output);
    }



    
}
