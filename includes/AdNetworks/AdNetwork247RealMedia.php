<?php

class AdNetwork247RealMedia extends AdNetwork {

        public function getAd($slotname, $size, $network_options = array()){

		$thingy = $this->getThingy($size);
		$out = <<<EOT
<script type="text/javascript">
OAS_rn = new String (Math.random());
OAS_rns = OAS_rn.substring (2, 11);
document.write('<scr'+'ipt src="http://network.realmedia.com/RealMedia/ads/adstream_jx.ads/wikia/ros/$size/jx/ss/a/1'+OAS_rns+'@$thingy"><\/scr'+'ipt>');
</script>
EOT;
                return $out;
        }


	private function getThingy($size){
		// They have a special variable that correlates to size
		switch ($size){
			case '120x600': return 'x08';
			case '160x600': return 'x10';
			case '300x250': return 'x15';
			case '468x60': return 'Position1';
			case '728x90': return 'Top1';
			default: return '';
		}
	}

}
