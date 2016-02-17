<?php

ini_set("include_path", '/home/kinztech/php:' . ini_get("include_path") );
require_once "HTTP/Request2.php";
/**
 * Created by PhpStorm.
 * User: Allen Kinzalow
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

    /**
     * Construct a crawler based off a given url/query and a valid session cookie.
     * @param $query
     * @param $cookie_key
     */
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

    /**
     * For testing purposes.
     */
    public function print_request() {
        //<div class="_c24 _50f4">
        //echo htmlspecialchars($this->response->getBody());
        $match = $this->getTextBetweenTags($this->body, '');
        foreach($match as $m)
            echo $m.'<br>';
    }

    /**
     * Cut out a section from the body.
     * @param $toCut
     */
    public function cutSection($toCut) {
        $this->body = preg_replace($toCut, '', $this->body);
    }

    /**
     * Replace a section within the body.
     * @param $toCut
     * @param $toPaste
     */
    public function replaceSection($toCut, $toPaste) {
        $this->body = preg_replace($toCut, $toPaste, $this->body);
    }

    /**
     * Match a segement in the body using regex patterns.
     * @param $match
     * @return int
     */
    public function findMatch($match) {
        $m = preg_match($match, $this->body);
        return $m;
    }

    /**
     * Extract an the next available image source when given
     *  a piece of the body.
     * @param $toFind
     * @return string
     */
    public function extractImage($toFind) {
        $b = substr($this->body, strpos($this->body, $toFind));
        $src = substr($b, strpos($b, 'src="') + 5);
        $link = substr($src, 0, strpos($src, '"'));
        return $link;
    }

    /**
     * Extract the next available image source after the
     *  nth occurace of a piece of the body.
     * @param $toFind
     * @param $amount
     * @return string
     */
    public function extractImageAmount($toFind, $amount) {
        $b = $this->body;
        for($i = 0; $i < $amount; $i++) {
            $b = substr($b, strpos($b, $toFind) + strlen($toFind));
        }
        $src = substr($b, strpos($b, 'src="') + 5);
        $link = substr($src, 0, strpos($src, '"'));
        return $link;
    }

    /**
     * Extract the next available link when given a piece of the body.
     * @param $toFind
     * @return string
     */
    public function extractLink($toFind) {
        $b = substr($this->body, strpos($this->body, $toFind));
        $src = substr($b, strpos($b, 'href="') + 6);
        $link = substr($src, 0, strpos($src, '"'));
        return $link;
    }

    /**
     * Strip a link from the given string.
     * @param $link
     * @return string
     */
    public function stripLink($link) {
        if(strpos($link, '">') == -1)
            return $link;
        $l = substr($link, strpos($link, '">') + 2);
        return substr($l, 0, strpos($l, '<'));
    }

    /**
     * Strip all links from a given phrase.
     * @param $phrase
     * @return string
     */
    public function stripAllLink($phrase) {
        while(strpos($phrase, '<a') !== FALSE) {
            $begin = strpos($phrase, '<a');
            $end = strpos($phrase, '</a>') + 4;
            $s = substr($phrase, $begin, $end);
            $s = $this->stripLink($s);
            $phrase = substr($phrase, 0, $begin) . $s . substr($phrase, $end);
        }
        return $phrase;
    }

    /**
     * Extract a link from a piece of the body after the
     *  nth occurrences of the piece.
     * @param $toFind
     * @param $amount
     * @return string
     */
    public function extractLinkAmount($toFind, $amount) {
        $b = $this->body;
        for($i = 0; $i < $amount; $i++) {
            $b = substr($b, strpos($b, $toFind) + strlen($toFind));
        }
        $src = substr($b, strpos($b, 'href="') + 6);
        $link = substr($src, 0, strpos($src, '"'));
        return $link;
    }

    /**
     * Get all of the text between a starting tag.
     * @param $tag
     * @param string $remover   Remove a certain piece of the body before identifying the tag.
     * @param int $count    Remove n parts of the body.
     * @param string $endTag    Specify an end tag(assist in deviating away from using just the end tag for the tag provided).
     *                          If left empty, the end tag will be determined base on the given tag.
     * @return array Returns an array of valid candidates that fit between the tag and end tag.
     */
    public function getTextBetweenTags($tag, $remover = '', $count = 0, $endTag = '') {
        $c = $this->body;
        if(strlen($remover) > 0 && $count > 0) {
            for($i = 0; $i < $count; $i++)
                $c = substr($c, strpos($c, $remover) + strlen($remover));
        }
        $splits = explode(' ', $tag);
        $endTag = strlen($endTag) <= 0 ? '</'.substr($splits[0], 1).'>' : $endTag;
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

    /**
     * Same as getTextBetweenTags with the adding functionality of defining the body to be searched.
     * @param $body
     * @param $tag
     * @param string $remover
     * @param int $count
     * @param string $endTag
     * @return array
     */
    public function getTextBetweenTagsInString($body, $tag, $remover = '', $count = 0, $endTag = '') {
        $c = $body;
        if(strlen($remover) > 0 && $count > 0) {
            for($i = 0; $i < $count; $i++)
                $c = substr($c, strpos($c, $remover) + strlen($remover));
        }
        $splits = explode(' ', $tag);
        $endTag = strlen($endTag) <= 0 ? '</'.substr($splits[0], 1).'>' : $endTag;
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