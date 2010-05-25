<?php
if (empty($_GET['tag_id'])){
        echo "tag_id is required";
        exit;
}
require dirname(__FILE__) . "/../includes/Framework.php";

$LiftiumConfig = new LiftiumConfig();
$data = $LiftiumConfig->loadTagFromId($_GET['tag_id']);
?>
<html>
<head>
<style type="text/css">
body {
        background: transparent;
        margin: 0px;
}
</style>
</head>
<body>
<script>
if (top != self && document.referrer.toString().indexOf(document.domain) > -1){
  // In an iframe and the parent window can be referenced
  var Liftium = top.Liftium;
  var LiftiumOptions = top.LiftiumOptions;
} else {
  <?php $options = Array('pubid' => '', 'autoInit' => false, 'placement' => @$_GET['placement']) ?>
  var LiftiumOptions = <?php echo json_encode($options) ?>;
  // FIXME: make this url configurable 
  document.write("<script src='http://liftium.wikia.com/js/Liftium.js'><\/script>");
}
</script>
<script>window.LiftiumPlacement = "<?php echo addslashes(@$_GET['placement'])?>";</script>
<?php if ($data['network_name'] == 'DART'){ ?>
<script src='http://liftium.wikia.com/js/Wikia.js'></script>
<?php } 
echo "<!-- Begin Tag #{$_GET['tag_id']} -->\n";
echo $data['tag'];
?>
</body>
</html>
