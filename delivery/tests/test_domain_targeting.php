<?php $LiftiumOptions = array("pubid" => 1046, "domain" => "delivery.dev.liftium.com") ?>
<?php require 'header.php'?>
This page is for testing domain targeting
<p>

<div id="slot1" class="adunit" style="width:180px; height: 150px;">
        <script>Liftium.callAd("180x150")</script>
</div>
<script>
Liftium._("slot1").innerHTML.match(/This is a fill from Tag \#101/) ?  LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

