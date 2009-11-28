<?php $LiftiumOptions = array("offline" => true) ?>
<?php require 'header.php'?>
This page is for testing javascript cookie setting. 
<p>
<script>
function cookieTest(cookieName, expires ){
	var cookieValue = Liftium.cookie(cookieName), options;
	if (cookieValue === null){
		document.write(cookieName + " not set yet" + "<br />");
		cookieValue = 0;
	} else if (parseInt(cookieValue, 10) === NaN){
		document.write("Something is wrong with the value of " + cookieName + " : " + cookieValue + "<br />");
		LiftiumTest.testFailed();
	} else {
		document.write("Current value of " + cookieName + " from previous test:" + cookieValue + "<br />");
		LiftiumTest.testPassed();
	}

	if (expires){
		options = {expires: expires}
	} else {
		options = {}
	}

	Liftium.cookie(cookieName, parseInt(cookieValue, 10) + 1, options);

	cookieValue = Liftium.cookie(cookieName);

	document.write("New value for " + cookieName + ":" + cookieValue + "<br/>");
	if (parseInt(cookieValue, 10) >= 1){
		LiftiumTest.testPassed();
	} else {
		LiftiumTest.testFailed();
	}
}
cookieTest("test_cookie");
document.write("<hr />");
var d = new Date();
document.write("Current date:" + d.toUTCString() + "<br />");
cookieTest("test_cookie_expire_10_seconds", 10*1000);

document.write("<hr />");
cookieTest("test_cookie_expire_now_plus_one_day", 86400*1000);

document.write("<hr />");
d.setTime(d.getTime() + (5 * 86400 * 1000));
document.write("Date for test_cookie_expire_date_object:" + d.toUTCString() + " (should be 5 days from now)<br />");
cookieTest("test_cookie_expire_date_object", d);
</script>




<?php require 'footer.php'?>
