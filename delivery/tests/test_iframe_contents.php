<?php require 'header.php'?>
This page is for testing javascript Liftium.iframeContents
<p>
<iframe id="testiframe" width="200" height="200">No iframe support text</iframe>
<script>
iframe = document.getElementById("testiframe");
var html = "HTML inside the iframe."; 
Liftium.iframeContents(iframe, html);
if (Liftium.iframeContents(iframe) == html){
	LiftiumTest.testPassed();
} else {
	alert("Liftium.iframeContents(iframe) = " + Liftium.iframeContents(iframe));
	LiftiumTest.testFailed();
}
// Test script
LiftiumTest.testPassed();
</script>

<?php require 'footer.php'?>
