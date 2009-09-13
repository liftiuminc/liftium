<?php require 'header.php'?>
This page is for testing javascript cookie setting. 
<p>
<script>
var cookieValue = Lift.cookie("cookie_test");
if (cookieValue === null){
	document.write("cookie_test not set yet" + "<br />");
	cookieValue = 0;
} else if (parseInt(cookieValue, 10) === NaN){
	document.write("Something is wrong with the value of the cookie: " + cookieValue + "<br />");
	LiftTest.testFailed();
} else {
	document.write("current cookie_test value from previous test:" + cookieValue + "<br />");
	LiftTest.testPassed();
}

Lift.cookie("cookie_test", parseInt(cookieValue, 10) + 1, {});

cookieValue = Lift.cookie("cookie_test");

document.write("new cookie_test value test:" + cookieValue + "<br/>");
if (parseInt(cookieValue, 10) >= 1){
	LiftTest.testPassed();
} else {
	LiftTest.testFailed();
}
</script>

<?php require 'footer.php'?>
