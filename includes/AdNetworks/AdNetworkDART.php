<?php
class AdNetworkDART extends AdNetwork {

	public function getAd($slotname, $size, $network_options=array()){
		$div = "DART_" . mt_rand(1,1000);
		$out = '<div id="' . $div . '">
			<script type="text/javascript">
			if (!Athena.e(window.iframeSlotname)){
				// In an iframe
				var DARTslot = iframeSlotname;
			} else {
				var DARTslot = Athena.slotname;
			}
			var creative = AthenaDART.callAd(DARTslot, "' . addslashes($size) . '")' . '
			document.write(creative);
			</script>
			</div>';
		$out .= '<script>
			if (document.getElementById("' . $div . '").innerHTML.match(/(-grey\.gif|athenahop\.gif)/)){
				if (top != self){' .
					// Inside an iframe
					$this->loadScriptJs("http://athena-ads.wikia.com/athena/hop.js?from_dart_athenahop.gif") . "\n" .
				'} else {
					Athena.debug("athenahop.gif found from DART for " + DARTslot, 2);
					Athena.hop(DARTslot);
				}
			}
			</script>
			';
		return $out;
	}

}
