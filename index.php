<?php
/*

this page is for to setup your app and performing some request.

*/

require 'vendor/autoload.php';


use Shopify\Context;
use Shopify\Auth\FileSessionStorage;


Context::initialize(
   "your_api_key", // your api key here
   "api_secret_key", // your api scret key here
   "write_orders,write_products,write_inventory", // api scopes, what your app is granting access from shopify
   "http://127.0.0.1:8000", // your apps url
   new FileSessionStorage('/tmp/php_sessions'), // session storage path
   '2023-01', // api version
   true,
   false,
);



// you have to save the access token and shop name from installtiona part and then you have to use here to perform the request
$client = new Rest("shop_name", "access_token"); 
$response = $client->get('products'); // this will provide all the product list from the shop


print_r(json_encode($response->getDecodedBody()));



