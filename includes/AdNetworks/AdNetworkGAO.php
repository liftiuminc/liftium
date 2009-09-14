<?php

class AdNetworkGAO extends AdNetwork {

	public function needsIframeWrapper(){
		return false;
	}

        public function getAd($slotname, $size, $network_options = array()){


		$dim=AdTag::getHeightWidthFromSize($size);
	        // The id changs based on size
                switch ($size){
                  case '728x90': $id = 435; break;
                  case '300x250': $id = 391; break;
                  case '160x600': $id = 774; break;
                  case '120x600': $id = 436; break;
                  default:
                        $AdNetwork = new AdNetworkNull("Invalid size for AdNetworkGAO ($size)", true);
                        return $AdNetwork->getAd($slotname, $size, $network_options);
                }

		$url = "http://www.game-advertising-online.com/index.php?section=serve&id=$id";

                return $this->iframeHtml($url, $dim['width'], $dim['height']);
        }

}
