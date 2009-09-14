<?php

class AdNetworkCPX extends AdNetwork {

        public function getAd($slotname, $size, $network_options = array()){

		$url = "http://adserving.cpxinteractive.com/st?ad_type=iframe&ad_size=$size&section=541503";
		$dim=Athena::getHeightWidthFromSize($size);

		return $this->iframeHtml($url, $dim['width'], $dim['height']);

        }

}
