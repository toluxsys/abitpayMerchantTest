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
            "txref": "' . $txref . '",
            "email": "' . $user->email . '",
            "amount": ' . $request->amount . ',
            "redirect_url": "https://techplushost.com",
            "hook_url": "https://techplushost.com/api/abithook"
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

    public function redirectcallback($ref)
    {

        $curl = curl_init();

        curl_setopt_array($curl, array(
            CURLOPT_URL => 'https://abitpay.techplushost.com/merchant/verify/' . $ref,
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => '',
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 0,
            CURLOPT_FOLLOWLOCATION => true,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => 'GET',
            CURLOPT_HTTPHEADER => array(
                'Authorization: mk_ef12fd23d9d47be5',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
        return $response;

//        {
//            "status": true,
//    "message": "Verification successful",
//    "data": {
//            "status": 1,
//        "merchant_id": "6082e85e0f950c09f83e575d",
//        "trans_id": "meref8382328ffea12364378904561718521",
//        "payment_ref": "LkOltSyWpKrf21Soyqin72sR445Afv",
//        "email": "odejinmisa@newwavesecosystem.com",
//        "usdamount": "5",
//        "redirect_url": "https://hello.com",
//        "hook_url": "https://hook.com",
//        "date": "2021-05-18 20:49:03",
//        "created_at": "2021-05-18T20:49:03.516Z",
//        "address": "TEyULJLQ9bmFdq4tCwUwYCHn77PBYh3aAo",
//        "amountpaid": "0.001693",
//        "wallet": "ETH"
//    }
//}

        //continue from here

    }
}
