<?php

class AdNetworkOpenX extends AdNetwork {

        public function getTagOptions(){ return array('zoneid'); }

	public function getAd($slotname, $size, $network_options = array()) {
	
                if (empty($network_options['zoneid'])){
                        $NullAd = new AdNetworkNull("zoneid must be supplied", true);
                        return $NullAd->getAd($slotname, $size, array());
                }

		$out = "<script>
			if (typeof Athena == 'undefined' && typeof top.Athena != 'undefined'){
				var Athena = top.Athena;
			}
			if (typeof AthenaDART == 'undefined' && typeof top.AthenaDART != 'undefined'){
				var AthenaDART = top.AthenaDART;
			}
			var OpenXParams = [];
			if (top != self){ // in iframe
				OpenXParams.loc = document.referrer;
			} else {
				OpenXParams.loc = window.location;
				OpenXParams.referer = document.referrer;
			}
			if (!Athena.empty(document.context)){
				OpenXParams.context = document.context;
			}
			if (!Athena.empty(document.mmm_fo)){
				OpenXParams.mmo_fo = 1;
			}
			if (!Athena.empty(document.MAX_used)){
				OpenXParams.exclude = document.MAX_used;
			}
			OpenXParams.zoneid = '{$network_options['zoneid']}';
			OpenXParams.cb = Math.random();
			OpenXParams.block = 1;
			var OpenXUrl = 'http://wikia-ads.wikia.com/www/delivery/ajs.php?' + Athena.buildQueryString(OpenXParams, '&');
			OpenXUrl += '&source=' + AthenaDART.getAllDartKeyvalues('$slotname');

			document.write('\x3Cscript src=' + OpenXUrl + '>\x3C\/script>');
			</script>";
		return $out;

	}

}
