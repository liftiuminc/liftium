<?php require 'header.php'?>
This page is for testing Liftium.handleNetworkOptions, which sets special javascript up depending the network and what is passed in Liftium.options
<p>
<script>
LiftiumOptions = {
	google_ad_height : 90,
	google_ad_format : "728x90_as"
};
Liftium.handleNetworkOptions({"network_id":"1"}); // Google
if (google_ad_height == 90 && google_ad_format == "728x90_as") {
        LiftiumTest.testPassed();
} else {
        LiftiumTest.testFailed();
}
</script>
<?php require 'footer.php'?>
