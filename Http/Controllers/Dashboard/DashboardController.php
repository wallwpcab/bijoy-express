<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function adminDashboard(Request $request){
        return view('user.dashboard.admin-dashboard');
    }


    public function merchantDashboard(Request $request){
        return view('user.dashboard.merchant-dashboard');
    }
    

    public function dbDashboard(Request $request){
        return view('user.dashboard.delivery-boy-dashboard');
    }
}
