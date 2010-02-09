<?php $LiftiumOptions = array("pubid"=>1048);?>
<?php require 'header.php'?>
This page is for testing slot targeting
<p>

This ad should be tag #130, because it is slot 'slot1'.
<div id="slotx" class="adunit" style="width:234px; height: 60px;">
	<script>LiftiumOptions.slot = 'slot1'</script>
        <script>Liftium.callAd("234x60")</script>
</div>
<p>
This ad should be tag #131, because it's slot is blank
<div id="sloty" class="adunit" style="width:234px; height: 60px;">
	<!-- Intentionally don't clear the slot to make sure that the code is doing it after the call above -->
        <script>Liftium.callAd("234x60")</script>
</div>
<p>
This ad should be tag #131, because it is slot is 'slot2'
<div id="slotz" class="adunit" style="width:234px; height: 60px;">
	<script>LiftiumOptions.slot = 'slot2'</script>
        <script>Liftium.callAd("234x60")</script>
</div>
<script>
Liftium._("slotx").innerHTML.match(/Targetted to slot1/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium._("sloty").innerHTML.match(/NOT targetted to a slot/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium._("slotz").innerHTML.match(/NOT targetted to a slot/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

