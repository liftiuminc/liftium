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
LiftiumOptions.referrer = "http://search.yahoo.com/search?p=pet+diabetes+yahoo";
if ( Liftium.getReferringKeywords() == "pet diabetes yahoo"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Bing
LiftiumOptions.referrer = "http://www.bing.com/search?q=pet+diabetes+bing";
if ( Liftium.getReferringKeywords() == "pet diabetes bing"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Ask
LiftiumOptions.referrer = "http://www.ask.com/web?q=pet+diabetes+ask";
if ( Liftium.getReferringKeywords() == "pet diabetes ask"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
// Google
LiftiumOptions.referrer = "http://www.google.com/?q=pet+diabetes+google";
if ( Liftium.getReferringKeywords() == "pet diabetes google"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}


// Make sure that it remembered the last one with a cookie
LiftiumOptions.referrer = "";
if ( Liftium.getReferringKeywords() == "pet diabetes google"){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>

