<?php
/* Don't use header.php, because we don't want Liftium to be loaded already  */
ini_set('display_errors', true);
error_reporting(E_ALL);
?>
<html>
<head>
<title>Liftium Test</title>
</head>
<script type="text/javascript" src="LiftiumTest.js"></script>
<style type="text/css">
.adunit {
	border: 1px dashed black;
        margin: 10px;
}       
</style>
<div id="test_results" style="display:none">Running tests...</div>
<!-- End header -->


This page is for testing the iframe delivery method, which is required because certain ad servers mangle our tags, using document.write, which doesn't allow for the internal config/geoiplookup calls to block. 
<p>
<div class="adunit" style="width:234px;height:60px">
<iframe onload="checkIframe()" src="http://nick.dev.liftium.com/tag_iframe?pubid=1042&size=234x60" width="234" height="60" noresize="true" scrolling="no" frameborder="0" marginheight="0" marginwidth="0" style="border:none" target="_blank"></iframe>
</div>

<script>
function checkIframe() {
      // The add that is called should call top.LiftiumTest.testPassed()
      if (LiftiumTest.testsRun < 1 ) {
	      LiftiumTest.testFailed();
      }
}
</script>

</div>

<?php require 'footer.php'?>

