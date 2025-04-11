<?php
require 'vendor/autoload.php';
require 'app/config/oauth_config.php';
session_start();

$client = new Google_Client();
$client->setClientId($oauth_config['client_id']); 
$client->setClientSecret($oauth_config['client_secret']);
$client->setRedirectUri($oauth_config['redirect_uri']);
$client->addScope("email");
$client->addScope("profile");

// Redirect đến trang xác thực của Google
$authUrl = $client->createAuthUrl();
header('Location: ' . filter_var($authUrl, FILTER_SANITIZE_URL));

?>