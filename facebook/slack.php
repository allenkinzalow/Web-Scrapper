<?php

include_once ('FacebookAnalyzer.php');
include_once ('FacebookProfiler.php');
include_once ('FacebookSearcher.php');
/**
 * Created by PhpStorm.
 * User: Allen Kinzalow
 * Date: 2/15/2016
 * Time: 6:42 PM
 */

$token = 'VeHxEFDIMYPQpBcYUTQ3lplH';
$channel = 'facebook_scraper';

if($_SERVER['REQUEST_METHOD'] == 'POST') {
    if($_POST['token'] == $token/* && $_GET['channel'] == $channel*/) {
        header('content-type: application/json');
        $argument = $_POST['text'];
        $parts = explode(' ', $argument);
        $command = $parts[0];
        $argument = str_replace($command . ' ', '', $argument);
        $output = '';
        switch($command) {
            case 'search':
                $searcher = new FacebookSearcher($argument);
                $output = $searcher->getStack();
                break;
            case 'user':
                $searcher = new FacebookProfiler($argument);
                $searcher->analyze();
                $output = $searcher->getSlack();
                break;
            default:
                $output = 'Invalid command, please try again.';
                break;
        }

        $response = array(
            'text' => 'The following information was found: ',
            'mrkdwn' => 'true',
            'attachments' => $output
        );

        //echo json_encode($response);

        $options = array(
            'http' => array(
                'method'  => 'POST',
                'content' => json_encode( $response ),
                'header'=>  "Content-Type: application/json\r\n" .
                    "Accept: application/json\r\n"
            )
        );

        $context  = stream_context_create( $options );
        $result = file_get_contents($_POST['response_url'], false, $context );
    }
}

function slack_format($string) {
    $string = str_replace('<br>','\n',$string);
    return $string;
}