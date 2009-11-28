<?php $LiftiumOptions = array("pubid" => 1042, "exclude_tags" => array(110)) ?>
<?php require 'header.php'?>
This page is for testing LiftiumOptions.exclude_tags 
<p>

<div id="slot1" class="adunit" style="width:125px; height: 125px;">
        <script>Liftium.callAd("125x125")</script>
</div>
<script>
Liftium._("slot1").innerHTML.match(/This is a fill from Tag \#111/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

