<?php
include ('FacebookCrawler.php');
/**
 * Created by PhpStorm.
 * User: Allen Kinzalow
 * Date: 2/13/2016
 * Time: 8:56 PM
 */
class FacebookProfiler implements FacebookAnalyzer {

    private $username;
    private $profile;

    private $current_cookie = '';
    private $link = 'https://www.facebook.com/';

    private $response;
    private $slack = array(array());

    function __construct($username)
    {
        $this->username = $username;
        $this->profile = $this->link . $this->username;
    }

    public function analyze() {
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
        $this->response = $this->response . '<br><b>Overview<b><br>';

        $categories = $crawler->getTextBetweenTags('<div class="_6a _5u5j _6b">', '', 0, '</li>');
        $i = 0;
        $fields = array(array());
        foreach($categories as $c) {
            $c = '.'.$c;
            $titles = $crawler->getTextBetweenTagsInString($c, '<div class="_c24 _50f4">');
            $sub_titles = $crawler->getTextBetweenTagsInString($c, '<div class="_50f8 _50f3">');
            $title = $titles[0];
            $sub_title = $sub_titles[0];
            $title = $this->format($title);
            $this->response = $this->response . $title .'<br>';
            $fields[$i]['title'] = $crawler->stripAllLink($title);

            $sub_title = $this->format($sub_title);
            $this->response = $this->response . $sub_title .'<br>';
            $fields[$i]['value'] = $crawler->stripAllLink($sub_title);
            $i++;
        }

        $this->slack[3]['title'] = 'Overview';
        $this->slack[3]['color'] = '#FFFB00';
        $this->slack[3]['fields'] = $fields;
    }

    function fetch_name() {
        $crawler = new FacebookCrawler($this->profile, $this->current_cookie);
        $name_a = $crawler->getTextBetweenTags('<span id="fb-timeline-cover-name">');
        $this->response = $this->response . '<b>Name: '.$name_a[0].'<b><br>';
        $this->slack[0]['title'] = 'Name';
        $this->slack[0]['text'] = $name_a[0];
        $this->slack[0]['color'] = '#41C6F2';
        $friends = $crawler->getTextBetweenTags('<span class="_50f8 _50f4">');
        /*if (strpos($friends[0], ' ') !== FALSE) {
            $f = explode(' ', $friends[0]);
            $friends[0] = $f[0];
        }*/
        $friend_count = $crawler->stripLink($friends[0]);
        $this->response = $this->response . '<b>Friends: '.$friends[0].'<b><br>';
        $this->slack[1]['text'] = 'Friends: ' . $friend_count;
        $this->slack[1]['color'] = '#FF002B';

        $link = $crawler->extractImage('<img class="profilePic img" ');
        $this->response = $this->response . '<b>Picture: <br><img src="'.$link.'"><b><br>';
        $this->slack[2]['title'] = 'Profile Picture: ';
        $this->slack[2]['image_url'] = $link;
        $this->slack[2]['color'] = '#1CE82D';
        //$this->slack[2]['mrkdwn_in'] = array('title', 'text');

    }

    function fetch_basic_info() {
        $sub_link = '/about?section=contact-info&pnref=about';
        $crawler = new FacebookCrawler($this->profile . $sub_link, $this->current_cookie);
        $titles = $crawler->getTextBetweenTags('<span class="_50f8 _50f4 _5kx5">');
        $values = $crawler->getTextBetweenTags('<span class="_50f4">');
        $this->response = $this->response . '<br><b>Basic Information: Found '.sizeof($titles).' Elements<b><br>';
        $this->slack[4]['title'] = 'Basic Information: ';
        $this->slack[4]['color'] = '#D400FF';
        $size = sizeof($titles);
        $fields = array(array());
        $message = '';
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
            $fields[$i]['title'] = $crawler->stripAllLink($titles[$i]);
            $fields[$i]['value'] = $crawler->stripAllLink($values[$i]);
        }
        $this->slack[4]['fields'] = $fields;
        $this->slack[4]['text'] = $message;
    }

    function fetch_family_relationship() {
        //_h72 lfloat _ohe _50f8 _50f7
        $sub_link = '/about?section=relationship&pnref=about';
        $crawler = new FacebookCrawler($this->profile . $sub_link, $this->current_cookie);
        $relationship = $crawler->getTextBetweenTags('<div class="_vb- _50f5">'); // status
        $relationship_1 = $crawler->getTextBetweenTags('<div class="_2lzr _50f5 _50f7">'); // person
        $rel = 'None';
        $this->slack[5]['title'] = 'Relationship: ';
        $this->slack[5]['color'] = '#FAA200';
        if(sizeof($relationship) > 0 || sizeof($relationship_1) > 0) {
            if(sizeof($relationship_1) > 0)
                $rel = $relationship_1[0];
            else if(sizeof($relationship) > 0)
                $rel = $relationship[0];
        }
        $rel = $this->format($rel);
        $this->response = $this->response . '<br>Relationship: '.$rel.'<br>';
        $this->slack[5]['text'] = $crawler->stripLink($rel);

        $family = $crawler->getTextBetweenTags('<span class="_50f5 _50f7">'); // person
        $this->response = $this->response . '<br>Family: '.sizeof($family).' found<br>';
        $this->slack[6]['title'] = 'Family: ';
        $this->slack[6]['color'] = '#40D6D6';
        $fields = array(array());
        $i = 0;
        foreach($family as $f) {
            $this->response = $this->response . $f . '<br>';
            $fields[$i]['value'] = $crawler->stripLink($f);
            $i++;
        }
        $this->slack[6]['mrkdwn_in'] = array('text');
        $this->slack[6]['fields'] = $fields;
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
        $string = str_replace('<div class="fsm fwn fcg">', '', $string);
        $string = str_replace('</div>', '', $string);
        $string = str_replace('<div>', '', $string);
        $string = str_replace('&#039;', '\'', $string);
        return $string;
    }

    public function getResponse() {
        return $this->response;
    }

    public function getSlack() {
        return $this->slack;
    }

}