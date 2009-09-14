<?php

class AdNetworkAdBrite extends AdNetwork {

	public $network_id = 3;

	public function getTagOptions(){
		return array('sid');
	}

        public function getAd($slotname, $size, $network_options=array()){
		
		if (empty($network_options['sid'])){
			$NullAd = new AdNetworkNull('Missing $sid for AdNetworkAdBrite::getAd()');
			return $NullAd->getAd($slotname, $size, $network_options);
		}
	
		$url = "http://ads.adbrite.com/mb/text_group.php?sid=" . urlencode($network_options['sid']) .  "&zs=" . $this->getZs($size);

		$out = '<script type="text/javascript">';

		if (@$_GET['skin'] == 'monaco' ){
                  // getAdColor only works in monaco
                  $out .= '
			if (typeof AdEngine != "undefined"){
			  var AdBrite_Title_Color = AdEngine.getAdColor("text");
			  var AdBrite_Text_Color =  AdEngine.getAdColor("text");
			  var AdBrite_Background_Color = AdEngine.getAdColor("bg");
			  var AdBrite_Border_Color = AdEngine.getAdColor("text");
			  var AdBrite_URL_Color = AdEngine.getAdColor("url");
			}';
		}
		$out .= '
			try{
				var AdBrite_Iframe=window.top!=window.self?2:1;
				var AdBrite_Referrer=document.referrer==""?document.location:document.referrer;AdBrite_Referrer=encodeURIComponent(AdBrite_Referrer);
			}catch(e){
				var AdBrite_Iframe="";var AdBrite_Referrer="";
			}
			var url = "' . $url . '&ifr="+AdBrite_Iframe+"&ref="+AdBrite_Referrer;' . "\n";
	
		// Make sure you do the document.write here so it stays in the frame
		$out.= "document.write('<script src=\"' + url + '\"><\/script>');" . "\n" .
			'</script>';
		return $out;
        }


	private function getZs($size){
		switch ($size){
		  case '300x250': return '3330305f323530';
		  case '160x600': return '3136305f363030';
		  case '728x90': return '3732385f3930';
		  default: return null;
		}
	}


	// Reporting
	public function login(){
		return false; // Using Michael's scraper instead

		if ($this->isLoggedIn){
			return true;
		}
		$this->fetchPage($this->webui_login_url);
		$login = $this->fetchPage("https://www.adbrite.com/mb/commerce/login.php", "POST",
			array(
				'username' => $this->webui_username,
				'pword' => $this->webui_password,
				'submit' => 'Sign in'
			)
		);
		$this->isLoggedIn = true;
	}


	public function getReportedCpmForTag($AdTag){
		$this->login();
		$results = $this->fetchPage("http://www.adbrite.com/zones/index.php?time_period=today&sortby=earnings&sortdir=desc&show_all=");
		
		if (! preg_match('/<tr class="(even|odd)" id="z' . $AdTag->options['sid'] . '">(.+)<\/tr>/s', $results, $match)){
			return false;
		}
		$rows = explode('</tr>', $match[2]);
		//print_r($rows[0]);
		if (! preg_match('/<td class=\'cpm\'>\$([0-9.]+)<br \/><\/td>/', $rows[0], $match2)){
			return false;
		} else {
			return floatval($match2[1]);
		}

	}


}
