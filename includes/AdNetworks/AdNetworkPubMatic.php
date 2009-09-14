<?php

class AdNetworkPubMatic extends AdNetwork {

	private $pubId = "15208";
	private $siteId = "15209";

	private $kadids = array(
		'skyscraper' => '9743',
		'leaderboard' => '9744',
		'boxad' => '9745'
	);

        public function getTagOptions(){ return array('kadid'); }

        public function getAd($slotname, $size, $network_options = array()){

		if (empty($network_options['kadid'])){
			$NullAd = new AdNetworkNull("kadid is required", false);
			return $NullAd->getAd($slotname, $size, $network_options);
		}

                $dim=AdTag::getHeightWidthFromSize($size);

                $out .= '<script type="text/javascript">
			var pubId="' . addslashes($this->pubId) . '";
			var siteId="' . addslashes($this->siteId) . '";
			var kadId="' . addslashes($network_options['kadid']) . '";
			var kadwidth="' . addslashes($dim['width']) . '";
			var kadheight="' . addslashes($dim['height']) . '";
			var kadtype=1;' .
			'</script>' . "\n" .
			$this->loadScript("http://ads.pubmatic.com/AdServer/js/showad.js");
                return $out;
        }

}
