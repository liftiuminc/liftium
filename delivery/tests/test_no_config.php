<?php $pubid = 1046; ?>
<?php require 'header.php'?>
This page is for testing Liftium ad calls if the config didn't download, which should display a PSA.
<p>
<script>
// Clear the config to simulate that it didn't download
Liftium.config = undefined;
</script>
<!-- sample page -->
<div class="pagesection" style="width:950px;">
 <div style="position: absolute; width:180px; float:left; border: 1px orange solid; padding:5px; font-size:10pt">
<h2>Left nav</h2>
  	<div id="slot1" class="adunit" style="width:160px; height: 600px">
  		<script>Liftium.callAd("160x600")</script>
	</div>
  </div>
  </div>
</div>
<script>
if (document.getElementById("slot1").innerHTML.toString().match(/Public Service/)){
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>
