<?php

include('TwitterExchangeAPI.php');

$settings = array(
    'oauth_access_token' => "2675313806-7CGWDZz9b9eRsJg0OSIQr3S4IHyfiWZNMvzQtLE",
    'oauth_access_token_secret' => "BJlm7RRzH0HXLUKHtBMkcThZA9AzXOzNZzozzdRxHFK8h",
    'consumer_key' => "'3DcQdGKeenMJNCQVJ2TxmJGws",
    'consumer_secret' => "04noUNJ6n4scN3KwDkEVKhajNIzFcMoeTAmCuO5vWdBaSGjQ3M"
);

$url = 'https://api.twitter.com/1.1/statuses/user_timeline.json?oauth_access_token=7CGWDZz9b9eRsJg0OSIQr3S4IHyfiWZNMvzQtLE&oauth_access_token_secret=BJlm7RRzH0HXLUKHtBMkcThZA9AzXOzNZzozzdRxHFK8h
consumer_key=3DcQdGKeenMJNCQVJ2TxmJGws&consumer_secret=04noUNJ6n4scN3KwDkEVKhajNIzFcMoeTAmCuO5vWdBaSGjQ3M&screen_name=twitterapi';
/*$getfield = '?screen_name=twitterapi';
$requestMethod = 'GET';

$twitter = new TwitterAPIExchange($settings);
echo $twitter->setGetfield($getfield)->buildOauth($url, $requestMethod)->performRequest();*/

echo file_get_contents($url);