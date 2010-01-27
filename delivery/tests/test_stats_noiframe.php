<?php require 'header.php'?>
This page is for testing Liftium ad stats, without iframes
<p>
<script>
// Clear the existing stats for idempotency
Liftium.tagStats = 'z';
</script>
<!-- sample page -->
<div id="slot1" class="adunit" style="width:728px; height: 90px;">
	<script>Liftium.callAd("728x90")</script> 
</div>
<script>
if (document.getElementById("slot1").innerHTML.toString().match(/This is a fill/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>
<div id="stats">
<script>
LiftiumTest.afterBeacon = function (){
	Liftium._("stats").innerHTML = LiftiumTest.getTagStats(1);
	Liftium._("stats").innerHTML += LiftiumTest.getTagStats(2);
	Liftium._("stats").innerHTML += LiftiumTest.getTagStats(3);

	Liftium.getTagStat(1, "a") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(2, "a") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(3, "a") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

	Liftium.getTagStat(1, "r") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(1, "l") == 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(2, "r") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(2, "l") == 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(3, "r") == 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
	Liftium.getTagStat(3, "l") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
};
</script>
</div>
<?php require 'footer.php'?>
