<?php

namespace App\Http\Controllers\Merchant\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class MerchantDashboardController extends Controller
{
   
    public function merchantDashboard(Request $request){
        return view('merchant.dashboard.merchant-dashboard');
    }
    

    
}
