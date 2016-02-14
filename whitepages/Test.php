<?php
include('WhitePages.php');
include('WhitePagesResult.php');
/**
 * Created by PhpStorm.
 * User: allen
 * Date: 2/13/2016
 * Time: 1:46 PM
 */

$whitepages = new WhitePages('537775405e1660bfb260b59ace642e37');
//$result = $whitepages->findPerson('Carl','Kinzalow','Phenix+City','al');
//$result = $whitepages->findPerson('Allen','Kinzalow','Phenix+City','al');
$result = $whitepages->findPerson('William','Gabler','Phenix+City','al');
$result->interpret();
$result->print_result();