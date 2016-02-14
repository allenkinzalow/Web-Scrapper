<?php

ini_set("include_path", '/home/kinztech/php:' . ini_get("include_path") );
require_once "HTTP/Request2.php";
/**
 * Created by PhpStorm.
 * User: allen_000
 * Date: 2/13/2016
 * Time: 6:16 PM
 */

class FacebookCrawler {

    private $query;
    private $cookie_key;
    private $request;
    private $response;
    private $body;

    //https://www.facebook.com/Awubis?ref=br_rs
    //https://www.facebook.com/search/top/?q=

    function __construct($query, $cookie_key)
    {
        $this->query = $query;
        $this->cookie_key = $cookie_key;
        $this->request = new HTTP_Request2($this->query);
        $this->request->setConfig(array(
            'ssl_verify_peer'   => FALSE,
            'ssl_verify_host'   => FALSE
        ));
        $this->request->setMethod('GET');
        $this->request->setHeader('cookie', $cookie_key);
        $this->response = $this->request->send();
        $this->body = $this->response->getBody();
    }

    public function print_request() {
        //<div class="_c24 _50f4">
        //echo htmlspecialchars($this->response->getBody());
        $match = $this->getTextBetweenTags($this->body, '');
        foreach($match as $m)
            echo $m.'<br>';
    }

    public function cutSection($toCut) {
        $this->body = preg_replace($toCut, '', $this->body);
    }

    public function replaceSection($toCut, $toPaste) {
        $this->body = preg_replace($toCut, $toPaste, $this->body);
    }

    public function findMatch($match) {
        $m = preg_match($match, $this->body);
        return $m;
    }

    public function extractImage($toFind) {
        $b = substr($this->body, strpos($this->body, $toFind));
        $src = substr($b, strpos($b, 'src="') + 5);
        $link = substr($src, 0, strpos($src, '"'));
        return $link;
    }

    public function extractImageAmount($toFind, $amount) {
        $b = $this->body;
        for($i = 0; $i < $amount; $i++) {
            $b = substr($b, strpos($b, $toFind) + strlen($toFind));
        }
        $src = substr($b, strpos($b, 'src="') + 5);
        $link = substr($src, 0, strpos($src, '"'));
        return $link;
    }

    public function extractLink($toFind) {
        $b = substr($this->body, strpos($this->body, $toFind));
        $src = substr($b, strpos($b, 'href="') + 6);
        $link = substr($src, 0, strpos($src, '"'));
        return $link;
    }

    public function extractLinkAmount($toFind, $amount) {
        $b = $this->body;
        for($i = 0; $i < $amount; $i++) {
            $b = substr($b, strpos($b, $toFind) + strlen($toFind));
        }
        $src = substr($b, strpos($b, 'href="') + 6);
        $link = substr($src, 0, strpos($src, '"'));
        return $link;
    }

    public function getTextBetweenTags($tag, $remover = '', $count = 0) {
        $c = $this->body;
        if(strlen($remover) > 0 && $count > 0) {
            for($i = 0; $i < $count; $i++)
                $c = substr($c, strpos($c, $remover) + strlen($remover));
        }
        $splits = explode(' ', $tag);
        $endTag = '</'.substr($splits[0], 1).'>';
        $matches = array();
        $i = 0;
        while(strpos($c,$tag) != false) {
            $begin = substr($c,strpos($c,$tag));
            $matches[$i] = substr($begin, strlen($tag), strpos($begin,$endTag,0) - strlen($tag));
            $c = substr($begin, strpos($begin,$endTag) + strlen($endTag));
            $i++;
        }
        return $matches;
    }

    /*function getMatch($tag, $body) {
        $c = $body;
        $splits = explode(' ', $tag);
        $endTag = '</'.substr($splits[0], 1).'>';
        $matches = array();
        $i = 0;
        while(strpos($c,$tag) != false) {
            $begin = substr($c,strpos($c,$tag));
            $matches[$i] = substr($begin, strlen($tag), strpos($begin,$endTag,0) - strlen($tag));
            $c = substr($begin, strpos($begin,$endTag) + strlen($endTag));
            $i++;
        }
        return $matches;
    }*/

}