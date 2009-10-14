<?php require 'header.php'?>
This page is for testing the cross domain communication tools. It pulls up an iframe that will attempt to call the "testPassed" on the parent page.
<p>
<script>
XDM.debugOn = true;
XDM.allowedMethods = ["LiftiumTest.testPassed"];
function crossDomainMessage(message){
        if (typeof console != "undefined"){
                if (console.dir) { console.dir(message); }
        }
        if (message.data.match(/%22arg2%20data%22/)){ // simple check for args being passed
                XDM.executeMessage(message.data);       
        };
};
XDM.listenForMessages(crossDomainMessage);
</script>

<?php
// Note that I'm using the port here so that it's a different domain
$iframeUrl = "http://" . $_SERVER["HTTP_HOST"] . ":81". dirname($_SERVER['REQUEST_URI']) . '/iframe_cross_domain.html';
?>
<iframe name="iframetest" id="iframetest" src="<?php echo $iframeUrl?>" width="300" height="300"></iframe>

<script>
// If the page the tests are never run, that means it didn't work
LiftiumTest.afterBeacon = function (){
	if (LiftiumTest.testsRun == 0){
		LiftiumTest.testFailed();
	}
}
</script>

<?php require 'footer.php'?>
