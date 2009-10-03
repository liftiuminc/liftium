<?php $LiftiumOptions = array('pubid' =>  1046) ?>
<?php require 'header.php'?>

<!-- For this test, we are testing errors, so turn the default error catching off -->
<script>window.failTestOnError = false;</script>

This page is for testing how we catch and deal with javascript errors in a tag.
<pre>
<script>
</script>

<!-- Error with a particular tag. Should call tag id #, which is a tag with bad javascript.  
-->
<script>Liftium.callAd("125x125");</script>
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
	document.write("This browser doesn't support onerror. :(");
	LiftiumTest.testPassed();
} else if (Liftium.errorCount == 1){
	LiftiumTest.testPassed();
} else {
	alert("2nd test: Liftium.errorCount is " + Liftium.errorCount);
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>


