<?php require 'header.php'?>
This page is for testing Liftium ad calls, emulating a sample customer with 4 ads.
<p>

<!-- sample page -->
<div class="pagesection" style="width:950px;">
 <div style="width:180px; float:left; border: 1px orange solid; padding:5px; font-size:10pt">
<h2>Left nav</h2>
  	<div class="adunit" style="width:160px; height: 600px">
  		<script>Liftium.callAd("160x600")</script>
	</div>
  </div>
 <div style="padding:5px; margin-left: 200px">
  	<div class="adunit" style="width:728px; height: 90px;">
		<script>Liftium.callAd("728x90")</script> 
	</div>
	<h2> Content</h2>
  	<div class="adunit" style="width:300px; height: 250px;float:right">
		<script>Liftium.callAd("300x250")</script>
	</div>
	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum

	<p>
	Lorem ipsum dolor sit amet, consectetur adipisicing elit, sed do eiusmod tempor incididunt ut labore et dolore magna aliqua. Ut enim ad minim veniam, quis nostrud exercitation ullamco laboris nisi ut aliquip ex ea commodo consequat. Duis aute irure dolor in reprehenderit in voluptate velit esse cillum dolore eu fugiat nulla pariatur. Excepteur sint occaecat cupidatat non proident, sunt in culpa qui officia deserunt mollit anim id est laborum
	<p>
	<br clear="right">
	<hr />
  	<div class="adunit" style="width:728px; height: 90px;">
		<script>Liftium.callAd("728x90")</script>
	</div>
  </div>
</div>
<script>
LiftiumTest.testPassed();
</script>

<?php require 'footer.php'?>
