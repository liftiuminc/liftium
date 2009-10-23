<?php require 'header.php'?>
This page is for testing Liftium.iframesLoaded(). 
<script>
// No iframes yet
if (Liftium.iframesLoaded() === true ){
  LiftiumTest.testPassed();
} else {
  LiftiumTest.testFailed();
}
</script>

<div><ol id="list">
<li>While loading, <script>document.write("document.readyState = " + document.readyState);</script>
</div>
<script>
Liftium.addEventListener(window, "load", function () { document.getElementById("list").innerHTML += "<li>Onload fired - document.readyState is " + document.readyState ;});
Liftium.addEventListener(window, "DocumentReady", function () { document.getElementById("list").innerHTML += "<li>DocumentReady fired document.readyState is " + document.readyState; });
Liftium.addEventListener(window, "DOMContentLoaded", function () { document.getElementById("list").innerHTML += "<li>DOMContentLoaded fired document.readyState is " + document.readyState; });
</script>
<iframe id="fast" width="200" height="100" src="slow_loading_iframe.php?delay=1"></iframe>

<script>setTimeout("Liftium.iframesLoaded() === false ? LiftiumTest.testPassed() : LiftiumTest.testFailed()", 500);</script>
<script>document.write('<iframe width="200" height="100" src="slow_loading_iframe.php?delay=750"></iframe>');</script>
<script>
Liftium.iframesLoaded() === false ? LiftiumTest.testPassed() : LiftiumTest.testFailed()
</script>

<iframe width="200" height="100" src="slow_loading_iframe.php?delay=750"></iframe>
<script>
Liftium.iframesLoaded() === false ? LiftiumTest.testPassed() : LiftiumTest.testFailed()
</script>
<script>
setTimeout("Liftium.iframesLoaded() === true ? LiftiumTest.testPassed() : LiftiumTest.testFailed()", 2500);
</script>

<?php require 'footer.php'?>
