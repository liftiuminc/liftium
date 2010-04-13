<?php $LiftiumOptions=array("pubid" => 1047); ?>
<?php require 'header.php'?>
This page is for testing the iframe inject method of calling ads
<p>
<!-- Place holder iframe -->
<div id="Liftium_336x280"> 
<iframe style="width:336px; height: 280px;" id="ad1"></iframe>
</div>

<script>Liftium.callInjectedIframeAd("336x280", document.getElementById("ad1"));</script>

<script>
function checkIframe(again) {
        var iframes = document.getElementsByTagName("iframe");
        for (var i = 0; i < iframes.length; i++ ){
                if (iframes[i].src.match(/\/tag\/\?tag_id=51/)){
                        return LiftiumTest.testPassed();
                }
        }
        // The window may not have loaded (Safari), try 1 more time.
        if (!again){
                window.setTimeout("checkIframe(true);", 250);
        }  else {
                return LiftiumTest.testFailed();
        }
}
window.onload=checkIframe;
</script>

<?php require 'footer.php'?>

