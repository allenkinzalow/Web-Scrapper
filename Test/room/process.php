<?php if (!isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_REFERER']!="http://your-site.com/path/to/chat.js") {
	die();
} ?>
<?php

	include_once ('../../facebook/FacebookAnalyzer.php');
	include_once ('../../facebook/FacebookProfiler.php');
	include_once ('../../facebook/FacebookSearcher.php');
	//include_once ('../../whitepages/WhitePages.php.php');
	//include_once ('../../whitepages/WhitePagesResult.php.php');

	$function = htmlentities(strip_tags($_POST['function']), ENT_QUOTES);
	$file = htmlentities(strip_tags($_POST['file']), ENT_QUOTES);

	function parseCommand($file,$message) {
		$output = '';
		if(startsWith($message, '/')) {
			$message = substr($message, 1);
			$splits = explode(' ', $message);
			$output = $splits[2];
			switch($splits[0]) {
				case 'facebook':
					switch($splits[1]) {
						case '-user':
							$analyzer = new FacebookProfiler($splits[2]);
							$analyzer->analyze();
							$output = $analyzer->getResponse();
							break;
						case '-search':
							$searcher = new FacebookSearcher($splits[2]);
							$output = $searcher->getResponse();
							break;
					}
					break;
				case 'whitespace':
					$name = $splits[1];
					$city = $splits[2];
					$state = $splits[3];
					//$whitepages = new WhitePages('537775405e1660bfb260b59ace642e37');

					break;
			}
			if(strlen($output) > 0)
				fwrite(fopen($file, 'a'), "<span>Salty Stalker</span>" .'<span class="test">'. $output = str_replace("\n", " ", $output).'</span>' . "\n");
			return true;
		}
		return false;
	}

	function startsWith($haystack, $needle) {
		// search backwards starting from haystack length characters from the end
		return $needle === "" || strrpos($haystack, $needle, -strlen($haystack)) !== false;
	}
    
  $log = array();
    
    switch ($function) {
    
    	 case ('getState'):
    	 
        	 if (file_exists($file)) {
               $lines = file($file);
        	 }
             $log['state'] = count($lines);
              
        	 break;	
        	 
    	 case ('send'):
    	 
		     $nickname = htmlentities(strip_tags($_POST['nickname']), ENT_QUOTES);
		     $patterns = array("/:\)/", "/:D/", "/:p/", "/:P/", "/:\(/");
			 $replacements = array("<img src='smiles/smile.gif'/>", "<img src='smiles/bigsmile.png'/>", "<img src='smiles/tongue.png'/>", "<img src='smiles/tongue.png'/>", "<img src='smiles/sad.png'/>");
			 $reg_exUrl = "/(http|https|ftp|ftps)\:\/\/[a-zA-Z0-9\-\.]+\.[a-zA-Z]{2,3}(\/\S*)?/";
			 $blankexp = "/^\n/";
			 $message = htmlentities(strip_tags($_POST['message']), ENT_QUOTES);
			 
    		 if (!preg_match($blankexp, $message)) {
            	
    			 if (preg_match($reg_exUrl, $message, $url)) {
           			$message = preg_replace($reg_exUrl, '<a href="'.$url[0].'" target="_blank">'.$url[0].'</a>', $message);
    			 } 
    			 $message = preg_replace($patterns, $replacements, $message);

				 /**
				  * Parse commands & replace with responses.
				  * -Allen
				  */
				 $command = parseCommand($file,$message);
				 if(!$command)
					 fwrite(fopen($file, 'a'), "<span>". $nickname . "</span>" . $message = '<span class="test">'.str_replace("\n", " ", $message)."</span>" . "\n");
    		 }
    		 
        	 break;
    	
    }
    
    echo json_encode($log);

?>