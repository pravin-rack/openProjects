<?php

class PageLoadResponse{
	public $url;
	public $start;
	public $finish;
	public $totalSeconds;
}

class PageLoad{
	private static $loadPage = 'http://openprojects.local/seoSimple/class/helpers/PageLoadTime.php';
	
	private $mh;
	private $curls = array();
	
	public function PageLoad(){
		$this->mh = curl_multi_init();
	}
	
	public function addPage($url){
		$url = self::$loadPage . '?url=' . urlencode($url);
		
		$ch = curl_init();
		curl_setopt($ch, CURLOPT_URL, $url);
		curl_setopt($ch, CURLOPT_HEADER, 0);
		curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
		
		curl_multi_add_handle($this->mh, $ch);
		
		array_push($this->curls, $ch);
	}
	
	public function exec(){
		// execute the handles
		$running = null;
		do {
			curl_multi_exec($this->mh, $running);
		} while($running > 0);
		
		$result = array();
		
		// get content and remove handles
		foreach($this->curls as $ch) {
			array_push($result, json_decode(curl_multi_getcontent($ch)));
			curl_multi_remove_handle($this->mh, $ch);
		}
			
		// all done
		curl_multi_close($this->mh);
		
		return $result;

	}
	
}

?>