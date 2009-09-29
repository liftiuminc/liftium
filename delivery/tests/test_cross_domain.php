<?php require 'header.php'?>
<script src="../js/XDM.js"></script>
This page is for testing the cross domain communication tools. It pulls up an iframe that will attempt to call the "testPassed" on the parent page.

If this page is white, it didn't work.
<p>

<?php
// Note that I'm using the port here so that it's a different domain
$iframeUrl = "http://" . $_SERVER["HTTP_HOST"] . ":81". dirname($_SERVER['REQUEST_URI']) . '/iframe_cross_domain.html';
?>
<script>
</script>
<iframe name="iframetest" id="iframetest" src="<?php echo $iframeUrl?>" width="300" height="300"></iframe>

<?php require 'footer.php'?>

<script>
XDM.debugOn = true;
function crossDomainMessage(message){
        if (typeof console != "undefined"){
                console.log("Message received in " + window.location.hostname);
                if (console.dir) { console.dir(message); }
        }
        XDM.allowedMethods = ["LiftiumTest.testPassed"];
        if (message.data.match(/%22arg2%20data%22/)){ // simple check for args being passed
                XDM.executeMessage(message.data);       
        };
};
XDM.listenForMessages(crossDomainMessage);

</script>

<?php require 'footer.php'?>
