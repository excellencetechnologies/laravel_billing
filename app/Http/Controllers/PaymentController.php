<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class PaymentController extends Controller
{
    public function payment()
    {
        \Stripe\Stripe::setApiKey ( env('STRIPE_SECRET') ); 

        $data = request()->all();
        $token = self::generateToken($data);
        
        try {
            $payDetails = \Stripe\Charge::create ( array (
                    "amount" => ( $data['amount'] * 100 ),
                    "currency" => "usd",
                    "source" => $token,
                    "description" => "Test payment." 
            ) );
            
            $response = ['error' => 0, 'message' => 'Payment Successfull !', 'data' => $payDetails];

        } catch ( \Exception $ex ) {
            $response = ['error' => 0, 'message' => $ex->getMessage()];
        }

        return response()->json($response);
    }

    public function generateToken($data)
    {   
        $result = \Stripe\Token::create(
            array(
                "card" => array(
                    "name" => $data['name'],
                    "number" => $data['card_number'],
                    "exp_month" => $data['month'],
                    "exp_year" => $data['year'],
                    "cvc" => $data['cvc']
                )
            )
        );
        
        $token = $result['id'];
        return $token;
    }
}
