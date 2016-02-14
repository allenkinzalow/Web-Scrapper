<?php

/**
 * Created by PhpStorm.
 * User: allen
 * Date: 2/13/2016
 * Time: 2:05 PM
 */
class WhitePagesResult
{

    private $json_result;
    private $result;

    private $names = array();
    private $preferred_name;
    private $age_start;
    private $age_end;
    private $gender;
    private $city;
    private $postal_code;
    private $zip4;
    private $state_code;
    private $country_code;
    private $address;
    private $receiving_mail;
    private $latitude;
    private $longitude;
    private $phones;
    private $related_entities;

    function __construct($json_result)
    {
        $this->json_result = $json_result;
        $decode = json_decode($this->json_result, true);
        $this->result = $decode['results'];
    }

    public function interpret() {
        if($this->result == null)
            return;
        $r = $this->result[0];
        $this->age_start = $r['age_range']["start"];
        $this->age_end = $r['age_range']["end"];
        $this->names = $r['names'][0];
        $this->preferred_name = $r['best_name'];
        $this->gender = $r['gender'];
        $l = $r['best_location'];
        $this->city = $l['city'];
        $this->postal_code = $l['postal_code'];
        $this->zip4 = $l['zip4'];
        $this->state_code = $l['state_code'];
        $this->country_code = $l['country_code'];
        $this->address = $l['address'];
        $this->receiving_mail = $l['is_receiving_mail'];
        $this->latitude = $l['lat_long']['latitude'];
        $this->longitude = $l['lat_long']['longitude'];
        $i = 0;
        foreach($l['legal_entities_at'] as $le) {
            $this->related_entities[$i] = $le['best_name'];
            $i++;
        }
        $i = 0;
        if(sizeof($r['phones']) > 0) {
            foreach ($r['phones'] as $ph) {
                $this->phones[$i] = $ph['phone_number'];
                $i++;
            }
        }
    }

    public function print_result() {
        echo 'Age: '.$this->age_start . ' - ' . $this->age_end;
        echo '<br>' .$this->names['first_name'];
        echo '<br>' .$this->names['middle_name'];
        echo '<br>' .$this->names['last_name'];
        echo '<br>Preferred Name: ' .$this->preferred_name;
        echo '<br>Gender: ' .$this->gender;
        echo '<br>Address: ' .$this->address;
        echo '<br>Related Entities:<br>';
        foreach($this->related_entities as $re) {
            echo '<br>'.$re;
        }
        foreach($this->phones as $ph) {
            echo '<br>Phone Number: '.$ph;
        }
        echo '<br>Latitude: '.$this->latitude;
        echo '<br>Longitude: '.$this->longitude;
    }

}