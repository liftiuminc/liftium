<?php $LiftiumOptions = array("beacon_error" => false) ?>
<?php require 'header.php';?>
<!-- For this test, we are testing errors, so turn the default error catching off -->
<script>window.failTestOnError = false;</script>

<div id="error_message"></div>

<script>
window.onerror = function (msg, url, line){
	if (Liftium.e(Liftium.errorCount)){
		Liftium.errorCount = 1;
	} else {
		Liftium.errorCount++;
	}
	Liftium._("error_message").innerHTML += "<pre>Error #" + Liftium.errorCount + ":<br/ >" + url + ":" + line + " => " + msg + "</pre>";
	return true; // Return false to tell the browser to not report the error
};
</script>

<!-- Error on the page -->
<script>
if (undefinedVar == true ){
	code();
}
</script>
</pre>
<hr />

<!-- Error off the page -->
<script>Liftium.throwError();</script>

<script>
/* ATTENTION: Certain browsers don't fire the onload handler.
  If this is the case, Liftium.errorCount will be undefined.
  For these browsers, we won't get errors passed back :(

  Firefox 3 - Yes
  Safari 4 - No
  IE 6 - Yes
  IE 7 - Yes
  IE 8 - Yes
  Chrome - No
  Opera - No
*/
if (typeof Liftium.errorCount == "undefined" ) {
	LiftiumTest.testPassed();
	document.write("This browser does not support window.onerror. :(");
} else if (Liftium.errorCount == 2){
	LiftiumTest.testPassed();
} else {
	alert("Liftium.errorCount is " + Liftium.errorCount);
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>


