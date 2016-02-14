<?php
include ('FacebookCrawler.php');
/**
 * Created by PhpStorm.
 * User: allen_000
 * Date: 2/13/2016
 * Time: 8:56 PM
 */
class FacebookAnalyzer {

    private $username;
    private $profile;

    private $current_cookie = 'datr=1WuqU4HNYPmw6_aL-ZRjoM1K; locale=en_US; c_user=100002203210589; fr=0c9TC5vduykbawKK5.AWVJnonxrC-tCumKds0JS-Q4ios.BVb78h.Bk.AAA.0.AWXfum-3; xs=236%3AHtbA2v9DHbsQUQ%3A2%3A1455407335%3A20737; csm=2; s=Aa493HujE99FwuxT.BWv8Do; pl=n; lu=Tha3rN_yRItZ96idBc8tQfgw; act=1455408693073%2F0; p=-2; presence=EDvF3EtimeF1455408703EuserFA21B02203210589A2EstateFDsb2F0Et2F_5b_5dElm2FnullEuct2F1455406736BEtrFA2loadA2EtwF3881579515EatF1455408702301G455408703810CEchFDp_5f1B02203210589F3CC; wd=1920x509';
    private $link = 'https://www.facebook.com/';

    private $response;

    function __construct($username)
    {
        $this->username = $username;
        $this->profile = $this->link . $this->username;
        $this->analyze();
    }

    function analyze() {
        $this->fetch_name();
        $this->fetch_work_education();
        $this->fetch_basic_info();
        $this->fetch_family_relationship();
        $this->fetch_details();
        $this->fetch_life_events();
    }

    function fetch_work_education() {
        $sub_link = '/about?section=overview&pnref=about';
        $crawler = new FacebookCrawler($this->profile . $sub_link, $this->current_cookie);
        $categories = $crawler->getTextBetweenTags('<div class="_c24 _50f4">');
        $this->response = $this->response . '<br><b>Overview<b><br>';
        foreach($categories as $c) {
            $c = $this->format($c);
            $this->response = $this->response . $c .'<br>';
        }
        $categories = $crawler->getTextBetweenTags('<div class="_50f8 _50f3">');
        foreach($categories as $c) {
            $c = str_replace('<div class="fsm fwn fcg">', '', $c);
            $c = str_replace('</div>', '', $c);
            $c = $this->format($c);
            $this->response = $this->response . $c .'<br>';
        }
    }

    function fetch_name() {
        $crawler = new FacebookCrawler($this->profile, $this->current_cookie);
        $name_a = $crawler->getTextBetweenTags('<span id="fb-timeline-cover-name">');
        $this->response = $this->response . '<b>Name: '.$name_a[0].'<b><br>';

        $friends = $crawler->getTextBetweenTags('<span class="_50f8 _50f4">');
        /*if (strpos($friends[0], ' ') !== FALSE) {
            $f = explode(' ', $friends[0]);
            $friends[0] = $f[0];
        }*/
        $this->response = $this->response . '<b>Friends: '.$friends[0].'<b><br>';

        $link = $crawler->extractImage('<img class="profilePic img" ');
        $this->response = $this->response . '<b>Picture: <br><img src="'.$link.'"><b><br>';

    }

    function fetch_basic_info() {
        $sub_link = '/about?section=contact-info&pnref=about';
        $crawler = new FacebookCrawler($this->profile . $sub_link, $this->current_cookie);
        $titles = $crawler->getTextBetweenTags('<span class="_50f8 _50f4 _5kx5">');
        $values = $crawler->getTextBetweenTags('<span class="_50f4">');
        $this->response = $this->response . '<br><b>Basic Information: Found '.sizeof($titles).' Elements<b><br>';
        $size = sizeof($titles);
        for($i = 0; $i < $size; $i++) {
            if (strpos($values[$i], '<div') !== FALSE)
                continue;
            if (strpos($values[$i], '<span>') !== FALSE)
                continue;
            if($i == $size - 1 && $i < sizeof($values))
                for($z = $i + 1; $z < sizeof($values); $z++)
                    $values[$i] .= ' - ' . $values[$z];
            $values[$i] = $this->format($values[$i]);
            $this->response .= $titles[$i] . ': ' . $values[$i] .'<br>';
        }
    }

    function fetch_family_relationship() {
        //_h72 lfloat _ohe _50f8 _50f7
        $sub_link = '/about?section=relationship&pnref=about';
        $crawler = new FacebookCrawler($this->profile . $sub_link, $this->current_cookie);
        $relationship = $crawler->getTextBetweenTags('<div class="_vb- _50f5">'); // status
        $relationship_1 = $crawler->getTextBetweenTags('<div class="_2lzr _50f5 _50f7">'); // person
        $rel = 'None';
        if(sizeof($relationship) > 0 || sizeof($relationship_1) > 0) {
            if(sizeof($relationship) > 0)
                $rel = $relationship[0];
            else if(sizeof($relationship_1) > 0)
                $rel = $relationship_1[0];
        }
        $rel = $this->format($rel);
        $this->response = $this->response . '<br>Relationship: '.$rel.'<br>';

        $family = $crawler->getTextBetweenTags('<span class="_50f5 _50f7">'); // person
        $this->response = $this->response . '<br>Family: '.sizeof($family).' found<br>';
        foreach($family as $f) {
            $this->response = $this->response . $f . '<br>';
        }
    }

    function fetch_details() {

        /*$sub_link = '/about?section=bio&pnref=about';
        $crawler = new FacebookCrawler($this->profile . $sub_link, $this->current_cookie);
        $sound = $crawler->getTextBetweenTags('<audio preload="auto">');
        $this->response .= 'Sound: ' . sizeof($sound);
        if(sizeof($sound) > 0) {
            $s = $sound[0];
            $s = str_replace('<source src="', '', $s);
            $s = str_replace('" type="audio/mpeg">', '', $s);
            $src = '<source src="'.$s.'" type="audio/mpeg">';
            $this->response .= 'Name Pronunciation: <br><audio controls>'.$src.'</audio><br>';
        }*/
    }

    function fetch_life_events() {
        $crawler = new FacebookCrawler($this->profile, $this->current_cookie);
        $pics = $crawler->getTextBetweenTags('<div class="uiScaledImageContainer" style="width:101px;height:101px;">');
        $this->response .= '<br>Sample of Pictures: <br>';
        $i = 0;
        foreach($pics as $p) {
            $this->response .= $p;
            $i++;
            if($i == 3)
                break;
        }

    }

    function format($string) {
        $string = preg_replace("/<\/?ul[^>]*\>/i", "", $string);
        $string = preg_replace("/<\/?span[^>]*\>/i", "", $string);
        $string = str_replace('<li>', '', $string);
        $string = str_replace('<>', '', $string);
        $string = str_replace('</li>', '', $string);
        $string = str_replace('</span>', '', $string);
        $string = str_replace('</ul>', '', $string);
        $string = str_replace('<div data-pnref="rel">', '', $string);
        $string = str_replace('</div>', '', $string);
        return $string;
    }

    public function getResponse() {
        return $this->response;
    }

}