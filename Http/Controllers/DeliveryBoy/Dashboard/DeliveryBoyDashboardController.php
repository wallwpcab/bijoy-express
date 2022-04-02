<?php

namespace App\Http\Controllers\DeliveryBoy\Dashboard;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class DeliveryBoyDashboardController extends Controller
{
      
    public function DeliveryBoyDashboard(Request $request){
        return view('deliveryBoy.dashboard.delivery-boy-dashboard');
    }
}
