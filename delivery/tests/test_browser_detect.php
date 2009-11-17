<?php $LiftiumOptions = array("offline" => true) ?>
<?php require 'header.php'?>
This page is for testing BrowserDetect
<p>
<script>
document.write("BrowserDetect.OS = " + BrowserDetect.OS + "<br />\n");
document.write("BrowserDetect.browser = " + BrowserDetect.browser + "<br />\n");
document.write("BrowserDetect.version = " + BrowserDetect.version + "<br />\n");
</script>

<script>
Liftium.e(BrowserDetect.OS) ? LiftiumTest.testFailed() :  LiftiumTest.testPassed();
Liftium.e(BrowserDetect.browser) ? LiftiumTest.testFailed() :  LiftiumTest.testPassed();
Liftium.e(BrowserDetect.version) ? LiftiumTest.testFailed() :  LiftiumTest.testPassed();
</script>

<?php require 'footer.php'?>

