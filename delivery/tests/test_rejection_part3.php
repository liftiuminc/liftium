<?php $pubid = 1046; ?>
<?php require 'header.php'?>
This page is for testing rejection time, part 2:<br />
Modify the tag stats to simulate elapsed time: first tag will hop again, second tag will always fill.
<p>
<script>
// Fiddle with the data to simulate time elapsed.
Liftium.tagStats = Liftium.cookie('ATS');
//document.write(Liftium.tagStats + '<br />');
Liftium.tagStats = Liftium.tagStats.replace(/(\d+_13l[^,]+m)(\d+)/, function (str, stat, time) { return stat + (parseInt(time) - 5); });
//document.write(Liftium.tagStats);
</script>
<!-- sample page -->
<div id="slot1" class="adunit" style="width: 300px; height: 250px;">
	<script>Liftium.callAd("300x250")</script> 
</div>
<script>
var output = document.getElementById("slot1").innerHTML.toString();
output.match(/This is a hop/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
output.match(/This is an always fill/) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();

</script>
<?php require 'footer.php'?>
