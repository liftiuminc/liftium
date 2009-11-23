<?php require 'header.php'?>
This page is for testing Liftium ad stats, with iframes
<p>
<script>
// Clear the existing stats for idempotency
Liftium.tagStats = '';
</script>
<!-- sample page -->
<div id="slot1" class="adunit" style="width:468px; height: 60px;">
	<script>Liftium.callAd("468x60")</script> 
</div>
<div id="stats"></div>
<script>
LiftiumTest.afterBeacon = function (){
	Liftium._("stats").innerHTML = LiftiumTest.getTagStats(60);
	Liftium._("stats").innerHTML += LiftiumTest.getTagStats(61);
	Liftium._("stats").innerHTML += LiftiumTest.getTagStats(62);

	Liftium.getTagStat(60, "a") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(61, "a") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(62, "a") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

	Liftium.getTagStat(60, "r") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(60, "l") == 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(61, "r") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(61, "l") == 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(62, "r") == 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(62, "l") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

	Liftium._("stats").innerHTML += "<xmp>" + Liftium.print_r(Liftium.chain) + "</xmp>";
};
</script>
<?php require 'footer.php'?>
