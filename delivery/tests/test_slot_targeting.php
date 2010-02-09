<?php $LiftiumOptions = array("pubid"=>1043);?>
<?php require 'header.php'?>
This page is for testing slot targeting
<p>

This ad should be tag #130, because it is slot 'slot1'.
<div id="slot1" class="adunit" style="width:180px; height: 150px;">
	<script>LiftiumOptions.slot = 'slot1'</script>
        <script>Liftium.callAd("234x60")</script>
</div>
<p>
This ad should be blank (an intentional error) , because it is slot is 'slot2', and there are no ads for slot2
<div id="slot2" class="adunit" style="width:180px; height: 150px;">
	<script>LiftiumOptions.slot = 'slot2'</script>
        <script>Liftium.callAd("234x60")</script>
</div>
<p>
This ad should be tag #131, because it's slot is blank
<div id="slot3" class="adunit" style="width:180px; height: 150px;">
	<script>LiftiumOptions.slot = ''</script>
        <script>Liftium.callAd("234x60")</script>
</div>
<script>
Liftium._("slot1").innerHTML.match(/Targetted to slot1/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium._("slot2").innerHTML.match(/No available ads/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium._("slot3").innerHTML.match(/NOT targetted to a slot/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

