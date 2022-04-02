<?php
namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use \Illuminate\Http\Response;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\Cookie;
use Illuminate\Support\Facades\Auth;

use Illuminate\Support\Facades\Hash;
use Illuminate\Auth\Events\Registered;
use App\Http\Requests\Auth\LoginRequest;

use Illuminate\Support\Str;
use Illuminate\Validation\Rules\Password;
use Illuminate\Support\Facades\Validator;



use App\Models\Admin;
use App\Models\User;
use App\Models\DeliveryBoy;
use App\Models\Address;
use App\Models\Merchant;



class AuthController extends Controller{
     /**
     * Show specified view.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function loginView(Request $request){

        $type = $request->role;
        if($type != 'admin' && $type != 'merchant' && $type != 'delivery_boy' || $type == null){
            return view('404');
        }
        

        if($request->cookie('remember_login_'.$type) != null){
            $cookie = json_decode($request->cookie('remember_login_'.$type));
                $role = $cookie->role;

                 
            if($role == 'admin'){

                $user = Admin::where('email', $cookie->email)->first();
    
            }elseif($role == 'delivery_boy'){
    
                $user = DeliveryBoy::where('email', $cookie->email)->first();
    
            }elseif($role == 'merchant'){
                
                $user = Merchant::where('email', $cookie->email)->first();
    
            }
            

            if($user){
        
                session([
                    'role' => $role, 
                    'user' => $user
                ]);
    
                return redirect($role.'/dashboard');
            }
        }

        if($type == 'delivery_boy'){
            $type = 'Delivery Boy';
        }

        return view('login.login', ['title' => ucfirst($type).' Sign In']);
    }

    /**
     * Authenticate login user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function login(Request $request, Response $response){
        
        $res['success'] = false;
        $res['message'] = "Server error please try again after some times.";

        $email = $request->email;
        $password = $request->password;
        $role = $request->role;
        $remember_me = $request->remember_me;
        

   
        if($role == 'admin'){
            // dd(check);
            $user = Admin::where('email', $request->email)->first();

        }elseif($role == 'delivery_boy'){

            $user = DeliveryBoy::where('email', $request->email)->first();

        }elseif($role == 'merchant'){
            
            $user = Merchant::where('email', $request->email)->first();

        }



        if($user){

            if($user->email_verified_at == NULL){
                $res['success'] = false;
                $res['message'] = 'Please verify your email first';
            }
            elseif(!Hash::check($password, $user->password)){
                $res['success'] = false;
                $res['message'] = 'Please your pss';
            }
            else{

               // $token = $request->user()->createToken('auth_token');


                session([
                    'role' => $role, 
                    'user' => $user
                ]);

                $res['success'] = true;
                $res['message'] = 'Logged in Successfully';
                $res['role'] = $role;

                if($remember_me == 'on'){

                    $expire = 60 * 24 * 30; //30 Day
                    Cookie::queue('remember_login_'.$role,  json_encode(['email' => $email, 'password' => $password, 'role' => $role]), $expire);
                }


            }
           
            
        }else{

            $res['message'] = 'Credentials Not Found';
            // $res['errors'] = 'Failed TO Login';


        }
        



        echo json_encode($res);
        return $response;
    }

    // public function changePassword(Request $request) {
    //     $merchant = Merchant::find(Session::get('user')->id);

    //     if(!Hash::check($request -> get ('cPassword'), $merchant->password)){
    //         echo "error, Your current password does not match with the password you provided. Please try again. \n";
    //         // return redirect()->back()->with("error", "Your current password does not matches with the password you provided. Please try again.\n"); // the passwords matches
    //     }if($request -> get('cPassword') === $request->get('new_password')){
    //         echo "error, New Password cannot be same as your current password. Please choose a different password\n";

    //         // return redirect->back()->with("error", "New Password cannot be same as your current password. Please choose a different password");
    //         // return redirect()->back()->with("error", "New Password cannot be same as your current password. Please choose a different password\n"); // the passwords matches

    //     }$validatedData = $request->validate([
    //         'cPassword' => 'required',
    //         'new_password'     => 'required', 
    //     ]);
    //     $merchant -> password = bcrypt($request -> get('new-password') );
    //     $merchant -> save();
    //     // return redirect() -> back() -> with("success", "Password changed successfully !");
        
    // }


    /**
     * Logout user.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function logout(Request $request, Response $response){
        $roleIs = session('role');
        $cookie = Cookie::forget('remember_login_'.$roleIs);
        $request->session()->flush();
        return redirect('login?role='.$roleIs)->withCookie($cookie);
    }

    public function registerView(Request $request){

        $type = $request->role;
        if($type != 'merchant' && $type != 'delivery_boy' || $type == null){

            return view('404');
        }
        if($type == 'delivery_boy'){
            $type = 'Delivery Boy';
        }
         return view('register.register', ['title' => ucfirst($type).' Registration']);
    }
    

    public function register(Request $request){


        $res['success'] = false;
        $res['message'] = "Server error please try again after some times";


        $rules = [
            'name'    => 'required|string|max:50',
            'email'         => 'required|string|email|max:255|unique:users',
            'role'          => 'required|string',
            'password'      => ['required', Password::min(6)],
            'password_confirmation'      => ['required', Password::min(6)],
            'city'          => 'required|string',
            'zipcode'          => 'required|string',
            'division'          => 'required|string',
        ];

        if($request->sub_type == 'Bank'){
            $rules2 = [
                'bank_name'    => 'required|string',
                'position_name'    => 'required|string'
            ];

            $rules = array_merge($rules, $rules2);

        }
    

        $validation = Validator::make( $request->all(), $rules );
        if ( $validation->fails() ) {
            return [
                'success' => false,
                'message' => 'Invalid form data.',
                'errors' => $validation->errors()
            ];
        }


        $role = $request->role;
        $name = $request->name;
        $email = $request->email;
        $phone1 = $request->phone1;
        $gender = $request->gender;
        $password = $request->password;
        $password_confirmation = $request->password_confirmation;
        $agree = $request->agree;

        $city = $request->city;
        $zipcode = $request->zipcode;
        $division = $request->division;

        if($agree == null){
            $res['message'] = 'Please accept our Privacy Policy';
            return json_encode($res);
        }

        if($password != $password_confirmation){
            $res['message'] = 'Confirm Password dose not match';
            return json_encode($res);
        }


        $remember_token = Str::random(10);
        

        if($role == 'merchant'){

            $type = $request->type;
            $sub_type = $request->sub_type;

            $dataSet1 = [
                    'type' => $type,
                    'sub_type' => $sub_type,
                    'name' => $name,
                    'email' => $email,
                    'phone1' => $phone1,
                    'password' => Hash::make($password),
                    'remember_token' => $remember_token
                ];
            $dataSet2 = [];


            if($sub_type == 'Bank'){
          
    
                $dataSet2 = [
                    'inst_name' => $request->bank_name,
                    'designation' => $request->position_name,
                ];
    
            }
            $finalData = array_merge($dataSet1, $dataSet2);

            $user = Merchant::create($finalData);

        }elseif($role == 'delivery_boy'){

            $user = DeliveryBoy::create([
                'name' => $name,
                'email' => $email,
                'phone1' => $phone1,
                'password' => Hash::make($password),
                'remember_token' => $remember_token
            ]);

        }

      // dd($user);

        $addressesOfUser = $user->address()->create([
            'user_id' => $user->id,
            'address_for' => $request->role,
            'city' => $request->city,
            'zipcode' => $request->zipcode,
            'division' => $request->division
        ]);

        $user->address_id = $addressesOfUser->id;
        $user->save();


        $token = $user->createToken('auth_token');

        /*
        $login_url = route('confirm_email').'?role='.$request->role.'&id='.$user->id.'&token='.$remember_token;
        $mailer = new Mailler($request->email, ucfirst($request->role).' Registration Successfully', 'registration', $data);
        $mailer->SendMail();
        */


        if(!$user){
            $res['success'] = false;
            $res['message'] = 'Failed To Registration. Please try again after some times';
        }elseif($user){
            $res['message'] = 'Registration Successfully Complete';
            $res['success'] = true;
        }

        return json_encode($res);
    }


}
