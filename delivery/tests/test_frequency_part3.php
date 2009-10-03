<?php $LiftiumOptions = array('pubid' => 1046); ?>
<?php require 'header.php'?>
This page is for testing frequency capping, part 1:<br />
Clear tag stats and do initial fill: first tag will fill.
<p>
<script>
// Fiddle with the data to simulate time elapsed.
Liftium.tagStats = Liftium.cookie('ATS');
//document.write(Liftium.tagStats + '<br />');
Liftium.tagStats = Liftium.tagStats.replace(/(\d+_15l)(\d+)/, function (str, stat, time) { return stat + '0'; });
//document.write(Liftium.tagStats);
</script>
<!-- sample page -->
<div id="slot1" class="adunit" style="width: 336px; height: 280px;">
	<script>Liftium.callAd("336x280")</script> 
</div>
<script>
var output = document.getElementById("slot1").innerHTML.toString();
output.match(/This is a fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
!output.match(/This is an always_fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

</script>
<?php require 'footer.php'?>
