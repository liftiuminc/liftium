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
<?php $options = Array('pubid' => '', 'autoInit' => false, 'placement' => @$_GET['placement']) ?>
<script>
var LiftiumOptions = <?php echo json_encode($options) ?>;
</script>
<!-- FIXME: make this url configurable -->
<script src='http://liftium.wikia.com/js/Liftium.js'></script>
<body>
<?php
if ($data['network_name'] == 'DART'){
        echo "<script src='http://liftium.wikia.com/js/Wikia.js'></script>\n";
}
echo "<!-- Begin Tag #{$_GET['tag_id']} -->\n";
echo $data['tag'];
?>
</body>
</html>
