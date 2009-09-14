<?php

class AdNetworkA4 extends AdNetwork {

        public function needsIframeWrapper() {
                return false;
        }       

        public function getAd($slotname, $size, $network_options = array()){

                $dim=Athena::getHeightWidthFromSize($size);
                switch ($size){
                  case '728x90': $adid = 405609; $dimid = 388861; break;
                  case '300x250': $adid = 409928; $dimid = 389637; break;
                  default:
                        $AdNetwork = new AdNetworkNull("Invalid size for AdNetworkA4 ($size)", true);
                        return $AdNetwork->getAd($slotname, $size, $network_options);
                }


		$url = "http://ads.ad4game.com/servlet/ajrotator/$adid/0/vh?z=ad4game&dim=$dimid&pv=";
		return $this->iframeHtml($url, $dim['width'], $dim['height']);
        }

}
