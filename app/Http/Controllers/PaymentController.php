<?php

namespace App\Http\Controllers;

use App\Customer;
use App\Http\Requests;
use Illuminate\Http\Request;

class PaymentController extends Controller
{

    public function pay(Request $request)
    {

        $token = $request['token'];
        $cutomer_id = $request['customer_id'];
        $total_price = $request['total'] * 100;

        $customer = Customer::find($cutomer_id);

    }

}
