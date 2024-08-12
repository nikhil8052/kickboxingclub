<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Http;


class MarianaController extends Controller
{
    

    public function checkPaymentProcess( Request $request ){



        try{
            $email="nikhilvelly852000@gmail.com";
            $firstName="Sagmetic";
            $lastName="Nikhil test";
            $password="password@123";

            return $this->createUserOnMarianatek($email, $firstName, $lastName, $password);
        }catch( \Exception $e ){
            return $e->getMessage();

        }




    }



    public function createUserOnMarianatek($email, $firstName, $lastName, $password)
    {
        $token = 'tbh9OP7rCOVeuac8jtAt6v2HTXnLjV';
        $userResponse = Http::withToken($token)
            ->asForm()
            ->post('https://kbxf.marianatek.com/api/users', [
                'email' => $email,
                'first_name' => $firstName,
                'last_name' => $lastName,
                'password' => $password,
                'home_location' => 48750,
            ]);

        if ($userResponse->failed()) {
            return ['success' => false, 'msg' => $userResponse->json()['errors'] ?? 'Unknown error'];
        }

        $userData = $userResponse->json();

        $cartResponse = Http::withToken($token)
            ->post('https://kbxf.marianatek.com/api/carts', [
                'data' => [
                    'type' => 'carts',
                    'attributes' => [
                        'fulfillment_partner' => '41362',
                        'status' => 'Open',
                        'user' => $userData['data']['id'],
                    ]
                ]
            ]);

        if ($cartResponse->failed()) {
            return ['success' => false, 'msg' => 'Error creating cart'];
        }

        $cartData = $cartResponse->json();

        $cartLineResponse = Http::withToken($token)
            ->post('https://kbxf.marianatek.com/api/cart_lines', [
                'data' => [
                    'attributes' => [
                        'quantity' => 1,
                        'options' => [],
                        'has_options' => false,
                    ],
                    'relationships' => [
                        'cart' => [
                            'data' => [
                                'type' => 'carts',
                                'id' => $cartData['data']['id'],
                            ]
                        ],
                        'partner' => [
                            'data' => [
                                'type' => 'partners',
                                'id' => '41362',
                            ]
                        ],
                        'product' => [
                            'data' => [
                                'type' => 'child_products',
                                'id' => 15856,
                            ]
                        ]
                    ],
                    'type' => 'cart_lines'
                ]
            ]);

        if ($cartLineResponse->failed()) {
            return ['success' => false, 'msg' => 'Error adding product to cart'];
        }

        $cartLineData = $cartLineResponse->json();

        return [
            'success' => true,
            'msg' => 'User created successfully',
            'cart_id' => $cartData['data']['id'],
            'user_id' => $userData['data']['id'],
            'full_name' => $firstName . ' ' . $lastName,
            'email_add' => $email,
            'cart_total' => $cartLineData['data']['attributes']['unit_subtotal'] ?? 0,
        ];
    }


    // Stripe intent 
    function createStripeSetupIntent( )
    {

        $token = 'tbh9OP7rCOVeuac8jtAt6v2HTXnLjV';

        $stripeIntent = Http::withToken($token)
            ->accept('application/json')
            ->post('https://kbxf.marianatek.com/api/stripe/v1/setup_intent', [
                'user' => 41347,
                'colletion_method' => 'terminal'
            ]);

        $res = $stripeIntent->json();

        $stripe_setup_intent_id=$res['stripe_setup_intent_id'];
        $client_secret=$res['client_secret'];
        $stripe_publishable_api_key=$res['stripe_publishable_api_key'];


        return view('stripe.payments', compact('stripe_publishable_api_key','client_secret','stripe_setup_intent_id'));
                
    }


    // store payment method 

    function storePaymentMethod( Request $req )
    {

        $token = 'tbh9OP7rCOVeuac8jtAt6v2HTXnLjV';

        $response = Http::withToken($token)
            ->accept('application/json')
            ->post('https://kbxf.marianatek.com/api/stripe/v1/store_payment_method', [
                'name' => 'test',
                'partner' => 41362,
                'payment_method' => $req->payment_method,
                'postal_code' => '121212',
                'user' => 41347
            ]);

        // if ($response->successful()) {

        //     return ['success' => true, 'data' => $response->json()];
        // }

        // return ['success' => false, 'error' => $response->json()];
        
        // Prepare the request data
        // $response = Http::withToken($token)
        //     ->post('https://kbxf.marianatek.com/api/stripe/v1/payment_intents', [
        //         'cart' => '40162',                               
        //         'amount' => '1.00',    
        //         'payment_method' =>$req->payment_method,                    
        //     ]);
        
        // // Check if the request was successful
        // if ($response->successful()) {
        //     return response()->json($response->json());
        // } else {
        //     return response()->json([
        //         'error' => $response->json()
        //     ], $response->status());
        // }


        // Prepare the request data
        $data = [
            'cart' => '41362',  // Example cart ID
            'amount' => '1.00', // Amount as a string
        ];

        // Send the request with JSON data
        $response = Http::withToken($token)
            ->accept('application/json')
            ->post('https://kbxf.marianatek.com/api/stripe/v1/payment_intents', $data);

        // Check if the request was successful
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => $response->json()
            ], $response->status());
        }


    }




    public function createPaymentIntent(Request $request)
    {
     

        $token = 'tbh9OP7rCOVeuac8jtAt6v2HTXnLjV';

           $response = Http::withToken($token)
            ->post('https://kbxf.marianatek.com/api/stripe/v1/payment_intents', [
                'cart' => '40162',                               
                'amount' => '1.00',    
                // 'payment_method' =>$req->payment_method,                    
            ]);
        
        // Check if the request was successful
        if ($response->successful()) {
            return response()->json($response->json());
        } else {
            return response()->json([
                'error' => $response->json()
            ], $response->status());
        }


    }



    public function confirmPayment( Request $req){

        return view('stripe.confirm_payment');
        
    }


}
