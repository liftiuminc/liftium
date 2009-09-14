<?php

class AdNetworkNetworkName extends AdNetwork {

        public function getAd($slotname, $size, $network_options = array()){

		if (empty($network_options['required_option'])){
			$NullAd = new AdNetworkNull("required_option is required", false);
			return $NullAd->getAd($slotname, $size, $network_options);
		}

		$out = " Put tag here";
                return $out;
        }

}
