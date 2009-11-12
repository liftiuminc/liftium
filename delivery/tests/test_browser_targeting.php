<?php require 'header.php'?>
This page is for testing browser targeting
<p>
<script>
document.write("BrowserDetect.OS = " + BrowserDetect.OS + "<br />\n");
document.write("BrowserDetect.browser = " + BrowserDetect.browser + "<br />\n");
document.write("BrowserDetect.version = " + BrowserDetect.version + "<br />\n");
</script>

<div id="slot1" class="adunit" style="width:180px; height: 150px;">
        <script>Liftium.callAd("180x150")</script>
</div>
<script>
b = BrowserDetect.browser;
if (b == "Firefox"){
	Liftium._("slot1").innerHTML.match(/Browser Targeting. Firefox/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
} else if (BrowserDetect.OS == "Windows" && b == "Explorer") {
	Liftium._("slot1").innerHTML.match(/Browser Targeting. Windows Explorer/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
} else {
	Liftium._("slot1").innerHTML.match(/Browser Targeting. Any browser/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>

