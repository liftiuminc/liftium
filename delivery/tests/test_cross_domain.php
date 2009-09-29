<?php require 'header.php'?>
<script src="../XDM.js"></script>
This page is for testing the cross domain communication tools. It pulls up an iframe that will attempt to call the "testPassed" on the parent page.
<p>

<?php
// Note that I'm using the ip address here so that it's a different domain
$iframeUrl = "http://" . gethostbyname($_SERVER["HTTP_HOST"]) . dirname($_SERVER['REQUEST_URI']) . '/iframe_cross_domain.html';
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
        XDM.allowedMethods = ["AthenaTest.testPassed"];
        if (message.data.match(/%22arg2%20data%22/)){ // simple check for args being passed
                XDM.executeMessage(message.data);       
        };
};
XDM.listenForMessages(crossDomainMessage);

function checkIfPassed(){
        clearInterval(myTimer);
        if (!document.title.match(/passed/i)){
                AthenaTest.testFailed();
        };
} 

myTimer = setInterval(checkIfPassed, 1000);
</script>

<?php require 'footer.php'?>
