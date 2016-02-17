<?php

/**
 * Created by PhpStorm.
 * User: Allen Kinzalow
 * Date: 2/13/2016
 * Time: 8:56 PM
 */
class FacebookSearcher implements FacebookAnalyzer
{

    private $query;

    private $current_cookie = '';
    private $link = 'https://www.facebook.com/search/people/?q=';
    private $search = '';

    private $response;
    private $stack = array(array());

    function __construct($query)
    {
        $query = str_replace('_', ' ', $query);
        $this->query = $query;
        $this->response .= 'Searching For: ' . $query .'<br>...<br>';
        $this->search = $this->link.$this->query;
        $this->analyze();
    }

    function analyze() {
        $crawler = new FacebookCrawler($this->search, $this->current_cookie);
        $results = $crawler->getTextBetweenTags('<div class="_5d-5">');
        $i = 1;
        $this->response .= 'Results: '.sizeof($results).'<br>';
        foreach($results as $r) {
            $link = $crawler->extractLinkAmount('<a class="_8o _8s lfloat _ohe"', $i);
            $image = $crawler->extractImageAmount(' aria-hidden="true" tabindex="-1">', $i);
            $username = str_replace('https://www.facebook.com/', '', $link);
            $username = substr($username, 0, strpos($username, '?'));
            $this->response .= '<br>Name: <a href="'.$link.'">' . $r .'</a><br>';
            $this->response .= 'Username: ' . $username .'<br>';
            $this->response .= 'Profile Picture: <br>';
            $this->response .= '<img src="'.$image.'" width="100" height="100"<br><br>';
            $this->stack[$i - 1]['title'] = 'Name: ' . $r;
            $this->stack[$i - 1]['text'] = 'Username: ' . $username;
            $this->stack[$i - 1]['image_url'] = 'Username: ' . $image;
            $this->stack[$i - 1]['color'] = '#556FA3';
            $i++;
        }
    }

    public function getResponse() {
        return $this->response;
    }

    public function getStack() {
        return $this->stack;
    }
}