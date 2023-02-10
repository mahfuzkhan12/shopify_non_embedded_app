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

