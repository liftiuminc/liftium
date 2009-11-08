<?php require 'header.php'?>
This page is for testing Liftium.getAdColor
<p>
<style type="text/css">
body {
	background-color: gray;
	color: #990000;
}
a {
	color: #FFF; /* test for 3 digit hex */
}
</style>
<a href="">test link</a>
<p>
<script>
document.write("bg color: " + Liftium.getAdColor("bg") + "<br />");
document.write("text color: " + Liftium.getAdColor("text") + "<br />");
document.write("link color: " + Liftium.getAdColor("link") + "<br />");
</script>

<script>
// IE returns "gray", other browsers return the hex num. Both are ok
(Liftium.getAdColor("bg") == "808080" ||  Liftium.getAdColor("bg") == "gray") ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getAdColor("text") == "990000" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getAdColor("link") == "FFFFFF" ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>
