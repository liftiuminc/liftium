<?php $pubid = 1046 ?>
<?php require 'header.php'?>

<!-- For this test, we are testing errors, so turn the default error catching off -->
<script>window.failTestOnError = false;</script>

This page is for testing how we catch and deal with javascript errors.
<ol>
<li>An error on the page
<li>An error in the Athena javascript 
<li>An error in a tag
<p>
<pre>
<!-- Error on the page -->
<script>
try { // Should fail on call to undefinedVar
	if (undefinedVar == true ){
		code();
	}
} catch (e) {
	var msg = Liftium.errorMessage(e);
	if (Liftium.e(msg)){
		LiftiumTest.testFailed();
		document.write("msg is empty!");
	} else {
		LiftiumTest.testPassed();
		// So I can see what it is on the various browsers
		document.write(msg);
	}
}
</script>
</pre>
<hr />

<!-- Error in Athena javascript -->
<script>Liftium.throwError();</script>

<!-- Error with a particular tag. Should call tag id #, which is a tag with bad javascript.  -->
<script>Liftium.callAd("125x125");</script>

<script>
	if (Liftium.errorCount == 2){
		LiftiumTest.testPassed();
	} else {
		LiftiumTest.testFailed();
	}
</script>
		

<?php require 'footer.php'?>

