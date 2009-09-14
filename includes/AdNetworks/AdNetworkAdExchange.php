<?php

class AdNetworkAdExchange extends AdNetwork {

        public function getTagOptions(){ return array('dc_slot'); }

        public function getAd($slotname, $size, $network_options = array()){

		if (empty($network_options['dc_slot'])){
			$NullAd = new AdNetworkNull("dc_slot is required", false);
			return $NullAd->getAd($slotname, $size, $network_options);
		}

		$url = "http://ad.doubleclick.net/adj/mktx;dc_slot={$network_options['dc_slot']};sz=$size" . ';ord=' . mt_rand() . '?';

		$out = $this->loadScript($url);
                return $out;
        }

}
