<?php

class AdNetworkGlam extends AdNetwork {

        public function getAd($slotname, $size, $network_options = array()){

		$url = "http://www2.glam.com/app/site/affiliate/viewChannelModule.act?mName=viewAdJs&affiliateId=357205835&adSize=$size&zone=Marketplace";

                return $this->loadScript($url);
        }

}
