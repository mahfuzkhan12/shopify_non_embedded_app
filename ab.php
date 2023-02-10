install.php 
<?php



if($_GET['shop'] && $_GET['shop'] != ''){


   $shop = $_GET['shop'];


   $api_key = "your_api_key"; // api key
   $scopes = "write_orders, write_products, write_inventory"; // your app's scopes, you should add only the scopes that you need for your app

   $redirect_uri = "http://127.0.0.1:8000/finalize_installation.php"; // redirect url, is this url you will redirected when the app is installed, and then you will get the access token and shop name to save 
  
   // Build install/approval URL to redirect to
   $install_url = "https://" . $shop . "/admin/oauth/authorize?client_id=" . $api_key . "&scope=" . $scopes . "&redirect_uri=" . urlencode($redirect_uri);
  
   // Redirect
   header("Location: " . $install_url); // this will redirect to shopify to install the app and after installation, shopify will redirect to the $redirect_url
   die();


}else {
   echo 'try again';
}



?>


finalize_installation.php

<?php

// users will be ridirected here from the shopify after the app installation
// you must save the access token and shop name for performing requests

// Set variables for our request
$api_key = "your_api_key"; // api key here
$shared_secret = "api_secret_here"; // api secret here
 

$params = $_GET; // Retrieve all request parameters
$hmac = $_GET['hmac']; // Retrieve HMAC request parameter


$params = array_diff_key($params, array('hmac' => '')); // Remove hmac from params
ksort($params); // Sort params lexographically
$computed_hmac = hash_hmac('sha256', http_build_query($params), $shared_secret);


// Use hmac data to check that the response is from Shopify or not
if (hash_equals($hmac, $computed_hmac)) {


   // Set variables for our request
   $query = array(
      "client_id" => $api_key, // Your API key
      "client_secret" => $shared_secret, // Your app credentials (secret key)
      "code" => $params['code'] // Grab the access key from the URL
   );
   // Generate access token URL
   $access_token_url = "https://" . $params['shop'] . "/admin/oauth/access_token";

   // Configure curl client and execute request
   $ch = curl_init();
   curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
   curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
   curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1);
   curl_setopt($ch, CURLOPT_URL, $access_token_url);
   curl_setopt($ch, CURLOPT_POST, count($query));
   curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($query));
   $result = curl_exec($ch);
   $httpcode = curl_getinfo($ch, CURLINFO_HTTP_CODE);


   curl_close($ch);


   // Store the access token
   $result = json_decode($result, true);

   $access_token = $result['access_token'];

   // Show the access token (don't do this in production!) 
   // you have to save this acces token somewhere to use for performing requests, you won't get the access token again
   // so careful in that case
   echo "acces token :" . $access_token; 


} else {
   // this request is not from shopify!
   die('This request is NOT from Shopify!');
}

?>


index.php

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

?>



please create a readme file from this files, this scripts is actually about shopify non-embedded app installation process

