<?php

class AdNetworkOpenXExchange extends AdNetwork {

        public function getTagOptions(){ return array('price'); }

        public function getAd($slotname, $size, $network_options = array()){

		$dim=Athena::getHeightWidthFromSize($size);

		if (empty($network_options['price'])){
			$network_options['price'] = "0.35";
		}

		$out = '<script type="text/javascript">
  var thorium = {
  "t":"",
  "f":"\<script type=\"text\/javascript\" src=\"http:\/\/athena-ads.wikia.com\/athena\/hop.js\"\>\<\/script\>"}
</script>';
		$out .= $this->loadScript("http://bid.openx.net/json?o=thorium&pid=648fd156-5bb5-4995-8d98-5fc20ac95b71&tag_type=1&f=" . $network_options['price'] . '&s=' . $size);

                return $out;
        }

}
