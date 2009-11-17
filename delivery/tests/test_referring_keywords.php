<?php $LiftiumOptions = array("disabled"=> true) ?>
<?php require 'header.php'?>
This page is for testing Liftium.getReferreringKeywords
<p>
<script>
// Google
if ( Liftium.getReferringKeywords("http://www.google.com/?q=pet+diabetes") == "pet diabetes google"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Yahoo!
if ( Liftium.getReferringKeywords("http://www.google.com/?q=pet+diabetes") == "pet diabetes yahoo"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Bing
if ( Liftium.getReferringKeywords("http://www.google.com/?q=pet+diabetes") == "pet diabetes bing"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Ask
if ( Liftium.getReferringKeywords("http://www.google.com/?q=pet+diabetes") == "pet diabetes ask"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}

// Make sure that it remembered the last one with a cookie
if ( Liftium.getReferringKeywords("blah") == "pet diabetes ask"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>

