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
