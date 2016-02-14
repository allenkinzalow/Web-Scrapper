<?php

/**
 * Created by PhpStorm.
 * User: allen_000
 * Date: 2/13/2016
 * Time: 8:56 PM
 */
class FacebookSearcher
{

    private $query;

    private $current_cookie = 'datr=1WuqU4HNYPmw6_aL-ZRjoM1K; locale=en_US; c_user=100002203210589; fr=0c9TC5vduykbawKK5.AWVJnonxrC-tCumKds0JS-Q4ios.BVb78h.Bk.AAA.0.AWXfum-3; xs=236%3AHtbA2v9DHbsQUQ%3A2%3A1455407335%3A20737; csm=2; s=Aa493HujE99FwuxT.BWv8Do; pl=n; lu=Tha3rN_yRItZ96idBc8tQfgw; act=1455408693073%2F0; p=-2; presence=EDvF3EtimeF1455408703EuserFA21B02203210589A2EstateFDsb2F0Et2F_5b_5dElm2FnullEuct2F1455406736BEtrFA2loadA2EtwF3881579515EatF1455408702301G455408703810CEchFDp_5f1B02203210589F3CC; wd=1920x509';
    private $link = 'https://www.facebook.com/search/people/?q=';
    private $search = '';

    private $response;

    function __construct($query)
    {
        $query = str_replace('_', ' ', $query);
        $this->query = $query;
        $this->response .= 'Searching For: ' . $query .'<br>...<br>';
        $this->search = $this->link.$this->query;
        $this->search();
    }

    function search() {
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
            $i++;
        }
    }

    public function getResponse() {
        return $this->response;
    }

}