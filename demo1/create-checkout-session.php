<?php

require '../vendor/autoload.php';
\Stripe\Stripe::setApiKey('sk_test_??????????????????????????????????????????????????????????');

header('Content-Type: application/json');

$YOUR_DOMAIN = 'https://taoka-toshiaki.com';
$postdata = (object)json_decode(file_get_contents('php://input'));

 $item = [];
 foreach($postdata->imgurl as $key=>$val){
     array_push($item,[
        'price_data' => [
        'currency' => 'jpy',
        'unit_amount' => $postdata->price[$key],
        'product_data' => [
            'name' => $postdata->prodcut[$key],
            'images' => [$postdata->imgurl[$key]],
        ],
        ],
        'quantity' => $postdata->quantity[$key]
    ]);
}

$checkout_session = \Stripe\Checkout\Session::create([
  'payment_method_types' => ['card'],
  'line_items' =>[$item],
  'mode' => 'payment',
  'success_url' => $YOUR_DOMAIN . '/Checkout/demo1/success.html',
  'cancel_url' => $YOUR_DOMAIN . '/Checkout/demo1/cancel.html',
]);

echo json_encode(['id' => $checkout_session->id]);
