<?php $LiftiumOptions = array("offline"=> true) ?>
<?php require 'header.php'?>
This page is for testing Liftium.getReferreringKeywords
<p>
<script>
// Clear!
Liftium.cookie("referringKeywords", ""); 
</script>

<script>
// Yahoo!
if ( Liftium.getReferringKeywords("http://search.yahoo.com/search?p=pet+diabetes+yahoo") == "pet diabetes yahoo"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Bing
if ( Liftium.getReferringKeywords("http://www.bing.com/search?q=pet+diabetes+bing") == "pet diabetes bing"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Ask
if ( Liftium.getReferringKeywords("http://www.ask.com/web?q=pet+diabetes+ask") == "pet diabetes ask"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Google
if ( Liftium.getReferringKeywords("http://www.google.com/?q=pet+diabetes+google") == "pet diabetes google"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}


// Make sure that it remembered the last one with a cookie
if ( Liftium.getReferringKeywords("blah") == "pet diabetes google"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>

