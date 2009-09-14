<?php

class AdNetworkZujo extends AdNetwork {

	public function needsIframeWrapper(){
		return false;
	}

        public function getAd($slotname, $size, $network_options = array()){

		$dim=Athena::getHeightWidthFromSize($size);

		$url = "http://ads.zujo.com/banners.php?size=" . $size;

                return $this->iframeHtml($url, $dim['width'], $dim['height']);
        }

}
