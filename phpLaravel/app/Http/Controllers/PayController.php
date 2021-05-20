<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;

class PayController extends Controller
{
    //
    public function payNow(Request $request)
    {
        $request->validate([
            'amount' => 'required',
        ]);

        $user = User::find(Auth::user()->id);
        $txref  = "abitref_".strtoupper(Str::random(12));

        $curl = curl_init();

        curl_setopt_array($curl, array(
        CURLOPT_URL => 'https://abitpay.techplushost.com/merchant/initialize',
        CURLOPT_RETURNTRANSFER => true,
        CURLOPT_ENCODING => '',
        CURLOPT_MAXREDIRS => 10,
        CURLOPT_TIMEOUT => 0,
        CURLOPT_FOLLOWLOCATION => true,
        CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
        CURLOPT_CUSTOMREQUEST => 'POST',
        CURLOPT_POSTFIELDS =>'{
            "txref": "tp1223yyierfyhje3e",
            "email": "abituser@gmail.com",
            "amount": 6,
            "redirect_url": "https://techplushost.com",
            "hook_url": "https://techplushost.com"
        }',
        CURLOPT_HTTPHEADER => array(
            'Authorization: mk_ef12fd23d9d47be5',
            'Content-Type: application/json'
        ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

        // return back()->with('success','Comment Posted Successfully');
    }
}
