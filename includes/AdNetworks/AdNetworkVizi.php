<?php

class AdNetworkVizi extends AdNetwork {

	function needsIframeWrapper(){
		return false;
	}

        public function getAd($slotname, $size, $network_options = array()){

		$dim=AdTag::getHeightWidthFromSize($size);

		$url = "http://ad.iconadserver.com/st?ad_type=iframe&ad_size=$size&section=572473";

                return $this->iframeHtml($url, $dim['width'] , $dim['height']);
        }

}
