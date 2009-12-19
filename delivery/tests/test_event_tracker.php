<?php require 'header.php'?>
This page is for testing event tracking
<p>
<!-- Standard ga call -->
<script type="text/javascript">
var gaJsHost = (("https:" == document.location.protocol) ? "https://ssl." : "http://www.");
document.write(unescape("%3Cscript src='" + gaJsHost + "google-analytics.com/ga.js' type='text/javascript'%3E%3C/script%3E"));
</script>
<script type="text/javascript">
var pageTracker = _gat._getTracker("UA-10292921-1");
pageTracker._setDomainName(".liftium.com");
pageTracker._trackPageview();
</script>
<script>
pageTracker._trackEvent("beacon", "load", "Test Publisher dash-1");
Liftium.trackEvent("beacon", "load", "Test Publisher dash-3");

</script>
<script>
if(Liftium.eventsTracked >= 1) {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>

