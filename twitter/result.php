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

    private $age_start;
    private $age_end;
    private $names = array();

    function __construct($json_result)
    {
        $this->json_result = $json_result;
        $decode = json_decode($this->json_result, true);
        $this->result = $decode['results'];
    }

    public function interpret() {
        if($this->result == null)
            return;
        $this->age_start = $this->result[0]['age_range']["start"];
        $this->age_end = $this->result[0]['age_range']["end"];
        $this->names = $this->result[0]['names'];
    }

    public function print_result() {
        echo $this->age_start . ' - ' . $this->age_end;
        echo '<br>' .$this->names['first_name'];
        echo '<br>' .$this->names['first_name'];
        echo '<br>' .$this->names['first_name'];
    }

}