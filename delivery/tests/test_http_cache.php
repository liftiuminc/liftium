<?php
require_once '../../includes/Framework.php';
$db = Framework::getDB();
$result = $db->query("SELECT UNIX_TIMESTAMP(MAX(updated_at)) as lastmod FROM tags;");
$row = $result->fetch();
Framework::httpCache($row[0]);
?>
<?php require 'header.php' ?>
This page is for testing the Framework::httpCache code.<br />
PHP says the current http time is: <?php echo Framework::httpDate(strtotime('now'))?><br />
PHP says the current lastmodified time of this test script is: <?php echo Framework::httpDate(filemtime(__FILE__))?><br />
PHP says the current lastmodified time of the tags table is : <?php echo Framework::httpDate($row[0])?><br />
Javascript says the last modified is <script>document.write(document.lastModified)</script>
<p>
<pre>
<?php
print_r($_SERVER);
?>
</pre>
<script>
var somethingGood = true;
if(somethingGood) {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>

