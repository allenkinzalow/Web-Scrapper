<?php

/**
 * Created by PhpStorm.
 * User: allen
 * Date: 2/13/2016
 * Time: 1:04 PM
 */
class WhitePages
{

    private $key;

    function __construct($key) {
        $this->key = $key;
    }

    public function findPerson($first_name, $last_name, $city, $state) {
        $service_url = 'https://proapi.whitepages.com/2.1/person.json?api_key='.$this->key.'&city='.$city.'&name='.$first_name.'+'.$last_name.'&state='.$state;
        //$service_url = "http://kinztech.com/chat/whitepages/input.txt";
        $wp_response = $this->fetch($service_url);
        return new WhitePagesResult($wp_response);
    }

    private function fetch($service_url) {
        $fetch_response = file_get_contents($service_url);
        return $fetch_response;
    }

}