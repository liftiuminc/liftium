<?php

class AdNetworkAmazon extends AdNetwork {

	public function needsIframeWrapper(){
		return false;
	}

        public function getTagOptions(){ return array('tracking_id', 'mode', 'search'); }

        public function getAd($slotname, $size, $network_options = array()){

		if (empty($network_options['tracking_id'])){
			// Default
			$network_options['tracking_id'] = "wikia0b-20";
		}

                $dim=Athena::getHeightWidthFromSize($size);
		

		$url = 'http://rcm.amazon.com/e/cm?' . 
			http_build_query(array(
				't' =>  urlencode($network_options['tracking_id']),
				'o' => '1', 
				'p' => $this->getP($size),
				'l' => 'st1',
				'mode' => urlencode(@$network_options['mode']),
				'search' => urlencode(@$network_options['search']),
				'fc1' => '000000',
				'lt1' => '', 
				'lc1' => '3366FF',
				'bg1' => 'FFFFFF',
				'f' => 'ifr')
			);

		return $this->iframeHtml($url, $dim['width'], $dim['height']);
        }

	public function getP($size){
		switch ($size){
		  case '300x250': return 12;
		  case '728x90': return 48;
		  default: return '';
		}
	}
		  

}
