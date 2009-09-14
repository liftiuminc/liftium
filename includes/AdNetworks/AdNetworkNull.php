<?php
/* This ad provider is effectively a no-op, just return a comment instead of any real code.
 * The idea is that errors with ads should not prevent the page from loading.
 * Null ad is also used in situations when the ads are not displayed, such
 * as when a user is logged in
 *
 * A message with the reason for not displaying the ad is passed into the constructor,
 * along with an optional argument to log it as an error.
 */

class AdNetworkNull extends AdNetwork {

	private $reason;

	/* @param reason - a note for why NULL ad is being used.
 	 * @param logError - whether to log this as an error
 	 */
	public function __construct($reason, $logError = false){
		$this->reason = $reason; 
		if ($logError){
			error_log("Null Athena Ad: $reason from {$_SERVER['HTTP_HOST']}{$_SERVER['REQUEST_URI']}", E_USER_WARNING);
		}
	}

	// Note that $slotname and $slot may not always be available.
	public function getAd($slotname, $slot, $network_options=array()) {
		$out = '<!-- Null Ad. Reason: ' . htmlspecialchars($this->reason) .
		       ', slotname=' . $slotname . ' -->';
		return $out;
	}

}
