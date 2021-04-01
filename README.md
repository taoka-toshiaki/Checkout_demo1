[![](https://img.youtube.com/vi/OiG05ka4vvE/0.jpg)](https://www.youtube.com/watch?v=OiG05ka4vvE)

実際は購入ボタン押下した時にデータは送信するのは製品IDと個数ぐらいにする必要があります。  
もしくはサーバサイドにデータを持つかだと思いますが、これはあくまでもDEMO決済のため、  
下記のようにしております。  

参考にしたサイト  
https://stripe.com/docs/checkout/integration-builder?canceled=true  
  
```php:create-checkout-session.php
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

```

```php:index.php
<!DOCTYPE html>
<html lang="ja">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<meta http-equiv="X-UA-Compatible" content="ie=edge">
	<meta name="Description" content="Enter your description here" />
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/css/bootstrap.min.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.2/css/all.min.css">
	<link rel="stylesheet" href="assets/css/style.css">
	<title>DEMO決済</title>
</head>

<body>
	<div class="container">
		<div class="row">
			<div class="col-12">
				<img src="https://taoka-toshiaki.com/img/81602409_166068694674459_6519251039000387270_n.jpg" class="imgurl img-fluid" style="width:150px;:height:150px" alt="製品A">
				<input type="hidden" class="prodcut" value="1234">
				<input type="hidden" class="quantity" value="2">
				<input type="hidden" class="price" value=1111>
				<span>値段￥1111</span>*<span>2個</span>
				<hr>
				<img src="https://taoka-toshiaki.com/img/507788.png" class="imgurl img-fluid" style="width:150px;:height:150px" alt="製品B">
				<input type="hidden" class="prodcut" value="5678">
				<input type="hidden" class="quantity" value="1">
				<input type="hidden" class="price" value=2222>
				<span>値段￥2222</span>*<span>1個</span>
				<hr>
				<button class="btn btn-primary" id="checkout-button" type="button">購入する</button>
			</div>
		</div>
	</div>

	<script src="https://cdnjs.cloudflare.com/ajax/libs/jquery/3.5.1/jquery.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/popper.js/1.16.1/umd/popper.min.js"></script>
	<script src="https://cdnjs.cloudflare.com/ajax/libs/twitter-bootstrap/4.6.0/js/bootstrap.min.js"></script>
	<script src="https://polyfill.io/v3/polyfill.min.js?version=3.52.1&features=fetch"></script>
	<script src="https://js.stripe.com/v3/"></script>
	<script type="text/javascript">
		// Create an instance of the Stripe object with your publishable API key
		var stripe = Stripe("pk_test_???????????????????????????????????????????????????");
		var checkoutButton = document.getElementById("checkout-button");
		checkoutButton.addEventListener("click", {
			data:() => {
				let imgurl = [];
				let prodcut = [];
				let quantity = [];
				let price = [];
				for (var i = 0; i < document.getElementsByClassName("imgurl").length; i++) {
					imgurl.push(document.getElementsByClassName("imgurl")[i].getAttribute("src"));
				}
				for (var i = 0; i < document.getElementsByClassName("prodcut").length; i++) {
					prodcut.push(document.getElementsByClassName("prodcut")[i].value);
				}
				for (var i = 0; i < document.getElementsByClassName("quantity").length; i++) {
					quantity.push(document.getElementsByClassName("quantity")[i].value);
				}
				for (var i = 0; i < document.getElementsByClassName("price").length; i++) {
					price.push(document.getElementsByClassName("price")[i].value);
				}
				console.log({
					imgurl: imgurl,
					prodcut: prodcut,
					quantity: quantity,
					price: price
				});
				return {
					imgurl: imgurl,
					prodcut: prodcut,
					quantity: quantity,
					price: price
				};
			},
			handleEvent: function() {
				fetch("./create-checkout-session.php", {
						method: "POST",
						headers: {
							"Content-Type": "application/json; charset=utf-8"
						},
						body: JSON.stringify(this.data())
					})
					.then(function(response) {
						return response.json();
					})
					.then(function(session) {
						return stripe.redirectToCheckout({
							sessionId: session.id
						});
					})
					.then(function(result) {
						// If redirectToCheckout fails due to a browser or network
						// error, you should display the localized error message to your
						// customer using error.message.
						if (result.error) {
							alert(result.error.message);
						}
					})
					.catch(function(error) {
						console.error("Error:", error);
					});
			}
		});
	</script>
</body>

</html>
```



