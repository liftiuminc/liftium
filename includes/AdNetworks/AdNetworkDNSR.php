<?php

class AdNetworkDNSR extends AdNetwork {

	function needsIframeWrapper(){
		return false;
	}

        public function getAd($slotname, $size, $network_options = array()){

		$dim=Athena::getHeightWidthFromSize($size);

		$url = "http://ad.z5x.net/st?ad_type=iframe&ad_size=$size&section=592776";

                return $this->iframeHtml($url, $dim['width'] , $dim['height']);
        }

}
