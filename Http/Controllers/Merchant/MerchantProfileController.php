<?php
use App\Http\Controllers\Controller;
// use App\Http\Controllers\Merchant\Validator;
// use App\Models\Validator;
namespace App\Http\Controllers\Merchant;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Merchant;
use App\Models\Address;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
// use Illuminate\Validation\Rules\Password;

use Illuminate\Support\Facades\Validator;



class MerchantProfileController extends Controller{
    public function merchantProfile(Request $request){
            $merchantId = Session::get('user');
            // dd($merchantId);
            $merchant = Merchant::find($merchantId->id);
            $merchantAddress = Address::firstWhere('user_id', $merchantId->id);

            
            $data['merchant'] = $merchant;
            $data['merchantAddress'] = $merchantAddress;
        return view('merchant.profile',$data);  
    }

    // public function edit($id){
    //     $blog = Blog::where('id',  '=', $id)->first();
    //         return view('merchant.profile', compact('blog'));
    // }

  
    public function postMerchantProfile(Request $request) {

        $res['success'] = false;
        $res['status'] = false;
        $res['message'] = "Please fix this following error";

        $rules = [
            'name'    => 'required|string',
            'email'    => 'required|string',
            'phone'    => 'required|string',
            'city'    => 'required|string',
            'zip'    => 'required|string',
        ];

        $validation = Validator::make( $request->all(), $rules );
            if ( $validation->fails() ) {
                return [
                    'success' => false,
                    'message' => 'Invalid form data.',
                    'errors' => $validation->errors()
            ];
        }

        $merchantId = $request->id;
       
        $name = $request->name;
        $email = $request->email;
        $phone    = $request->phone;
        $city     = $request->city;
        $country     = $request->country;
        $zipcode     = $request->zip;

        $update = Merchant::where('id', '=', $merchantId)->update([
            'name' => $name,
            'email' => $email,
            'phone1' => $phone
        ]);

        $updateAddress = Address::where('user_id', '=',$merchantId)->update([
            'country' => $country,
            'city' => $city,
            'zipcode' => $zipcode
        ]);
        
       
        $res['success'] = true;
        $res['status'] = true;
        $res['message'] = "Update";


       
        return response()->json($res);
    }


  

    public function changePassword(Request $request) {
        $merchant = Merchant::find(Session::get('user')->id);

        if(!Hash::check($request -> get ('cPassword'), $merchant->password)){
            echo "error, Your current password does not match with the password you provided. Please try again. \n";
            // return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.\n"); // the passwords matches
        }

        if($request -> get('cPassword') === $request->get('new_password')){
            echo "error, New Password cannot be same as your current password. Please choose a different password\n";

            // return redirect->back()->with("error", "New Password cannot be same as your current password. Please choose a different password");
            // return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password\n"); // the passwords matches

        }

        $validatedData = $request->validate([
            'cPassword' => 'required',
            'new_password'     => 'required', 
        ]);

        $merchant -> password = bcrypt($request -> get('new-password') );
        $merchant -> save();
        // return redirect() -> back() -> with("success", "Password changed successfully !");
        
    }


    




}


