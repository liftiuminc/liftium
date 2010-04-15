<?php require 'header.php'?>
This page is for testing LiftiumDART, a javascript way to generate dart calls (currently only used by Wikia)
<p>
<script src="/js/Wikia.js"></script>
<script>
ProviderValues = {};
ProviderValues.list = [{"name":"age","value":"teen"},{"name":"age","value":"yadult"},{"name":"egnre","value":"scifi"},{"name":"esrb","value":"everyone"},{"name":"esrb","value":"teen"},{"name":"gnre","value":"action"},{"name":"gnre","value":"adventure"},{"name":"media","value":"movie"},{"name":"pform","value":"all"},{"name":"sex","value":"m"}];
ProviderValues.string = "age=teen;age=yadult;egnre=scifi;esrb=everyone;esrb=teen;gnre=action;gnre=adventure;media=movie;pform=all;sex=m;";
</script>
<div class="adunit" style="width:728px;height:90px;">
<script>
document.write(LiftiumDART.callAd("TOP_LEADERBOARD", "728x90"));
</script>
</div>

<div class="adunit" style="width:300px;height:250px;">
<script>
document.write(LiftiumDART.callAd("TOP_RIGHT_BOXAD", "300x250"));
</script>
</div>

<?php require 'footer.php'?>

