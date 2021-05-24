<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Deposit;
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

        $dep['user_id'] = Auth::user()->id;
        $dep['amount'] = $request->amount;
        $dep['trans_id'] = $txref;
        Deposit::create($dep);

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
            "redirect_url": "https://techplushost.com/api/abitcallback",
            "hook_url": "https://techplushost.com/api/abithook"
        }',
            CURLOPT_HTTPHEADER => array(
                'Authorization: mk_ef12fd23d9d47be5',
                'Content-Type: application/json'
            ),
        ));

        $response = curl_exec($curl);

        curl_close($curl);
//        return $response;

        $rep = json_decode($response, true);

        return redirect()->away($rep['data']['payment_url']);
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
//        "email": "user@abitpay.com",
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
        $rep = json_decode($response, true);

        $deposit = Deposit::where('trans_id', $rep['data']['trans_id'])->where('status',0)->first();
        $deposit->payment_ref = $rep['data']['payment_ref'];
        $deposit->amount_paid = $rep['data']['amountpaid'];
        $deposit->coin_paid = $rep['data']['wallet'];
        $deposit->status = 1;
        $deposit->save();

        $user = User::where('id', $deposit->user_id)->first();
        $newbalance = $user->balance + $deposit->amount;
        $user->balance = $newbalance;
        $user->save();

        return redirect()->route('dashboard')->with("success", "Fund Deposit Successful");


    }
}
