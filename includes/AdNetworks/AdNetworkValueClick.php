<?php

class AdNetworkValueClick extends AdNetwork {

	public $network_id = 8;

        public function getTagOptions(){ return array('sid'); }


        public function getAd($slotname, $size, $network_options = array()){

		// These two variables switch based on size
		switch ($size){
		  case '728x90': $m = 1; $tp = 5; break;
		  case '300x250': $m = 6; $tp = 8; break;
		  case '160x600': $m = 3; $tp = 7; break;
		  case '120x600': $m = 3; $tp = 3; break;
		  default:
			$AdNetwork = new AdNetworkNull("Invalid size for AdNetworkValueClick ($size)", true);
			return $AdNetwork->getAd($slotname, $size, $network_options);
		}

		if (empty($network_options['sid'])){
			// Default accountid
			$network_options['sid'] = '27202';
		}
		
		$url = "http://media.fastclick.net/w/get.media?sid=" . $network_options['sid'] . "&m=$m&tp=$tp&d=j&t=s";

		return $this->loadScript($url);
        }



	public function login(){
		return false; // not working
		$this->fetchPage($this->webui_login_url);
		$login = $this->fetchPage("https://admin.valueclickmedia.com/v4/login.go", "POST",
			array(
				'login' => 'yes',
				'page' => 'home',
				'user_name' => $this->webui_username,
				'password' => $this->webui_password,
			)
		);
		echo $login;

				
		
	}
		
}
