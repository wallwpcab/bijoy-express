<?php

namespace App\Http\Controllers\Merchant\Parcel;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;

use App\Models\User;
use App\Models\DeliveryBoy;
use App\Models\Address;
use App\Models\Merchant;
use App\Models\Parcel;
use App\Models\ReceiverInformations;
use Illuminate\Support\Facades\Validator;

class MerchantParcelController extends Controller{


    public function merchantAddParcel(Request $request){
        return view('merchant.parcel.merchant-add-parcel');
    }
     

    public function merchantPostParcel(Request $request){


        $rules = [
            'name'    => 'required|string|max:50',
            'email'    => 'required|string|max:50',
            'phone'    => 'required|string|max:50',
            'city'    => 'required|string',
            'division'    => 'required|string',
            'zipcode'    => 'required|string',
        ];




        
        $validation = Validator::make( $request->all(), $rules );
            if ( $validation->fails() ) {
                return [
                    'success' => false,
                    'message' => 'Invalid form data.',
                    'errors' => $validation->errors()
            ];
        }

        $name = $request->name;
        $remail = $request->remail;
        $phone = $request->phone;
        $weight = $request->weight;
        $unit = $request->unit;
        $city = $request->city;
        $zipcode = $request->zipcode;
        $division = $request->division;
        $category = $request->category;
        $sensitivity = $request->sensitivity;
        $who_will_pay = $request->who_will_pay;
        $payment_method = $request->payment_method;
        $note = $request->note;

        $res['success'] = false;
        $res['message'] = "Server error please try again after some times";

        $userDetails = Session::get('user');
            $role = Session::get('role');
                $userAddressId = Address::where('user_id' , '=' , $userDetails->id)->first();

        

        if($request->sub_type == 'Bank'){
            $receiverInformations = ReceiverInformations::create([
                'name' => $userDetails -> name,
                'position' => $userDetails -> email,
            ]);

        }


        if($role == 'merchant'){
            $receiverInformations = ReceiverInformations::create([
                'name' => $userDetails->name,
                'email' => $userDetails->email,
                'zipcode' => $userAddressId->zipcode
            ]);

            $parcels = Parcel::create([
                'merchant_id' => $userDetails->id,
                'address_id' => $userAddressId->id,
                'receivers_details_id' => $receiverInformations->id,
                'parcel_type' => $category,
                'weight' => $weight,
                'unit' => $unit,
                'sensitivity' => $sensitivity,
                'who_will_pay' => $who_will_pay,
                'total_fee' => 29,
                'payment_method' => $payment_method,
                'payment_status' => 'pending',
                'note' => $note
                
            ]);

            $receiverInformations->parcels_id = $parcels->id;
            $receiverInformations->save();
        }elseif($role == 'delivery_boy'){

            $receiverInformations = ReceiverInformations::create([
                'name' => $name,
                'email' => $remail,
                'phone' => $phone,
                'city' => $city,
                'division' => $division,
                'zipcode' => $zipcode
            ]);

            $parcels = Order::create([
                'delivery_boy_id' => $userDetails->id,
                'address_id' => $userAddressId->id,
                'receivers_details_id' => $receiverInformations->id,
                'parcel_type' => $category,
                'weight' => $weight,
                'unit' => $unit,
                'sensitivity' => $sensitivity,
                'who_will_pay' => $who_will_pay,
                'total_fee' => 29,
                'payment_method' => $payment_method,
                'payment_status' => 'pending',
                'note' => $note
                
            ]);

            $receiverInformations->parcels_id = $parcels->id;
            $receiverInformations->save();

        }

        

        $res['success'] = true;
        $res['message'] = "Parcels Add Success";

        echo json_encode($res);
        echo "Not found";
    }

    
}
