<?php require 'header.php'?>
This page is for testing Liftium.isLoaded. 
<script>
if (Liftium.isLoaded() === false ){
  LiftiumTest.testPassed();
} else {
  Liftiumtest.testFailed();
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

<iframe width="200" height="100" src="slow_loading_iframe.php?delay=1000"></iframe>

<?php require 'footer.php'?>
