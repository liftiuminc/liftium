<?php require 'header.php'?>
This page is for testing setting of stats.
<p>
<script>
Liftium.tagStats = 'z'; // I can haz idemptonce
</script>
<script>
Liftium.setTagStat("1", "a");
Liftium.setTagStat("1", "l");
Liftium.setTagStat("2", "a");
Liftium.setTagStat("2", "l");
Liftium.setTagStat("1", "a");
Liftium.setTagStat("1", "l");
Liftium.setTagStat("1", "a");
Liftium.setTagStat("1", "r");

Liftium.getTagStat("1", "l") == 2 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getTagStat("2", "l") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getTagStat("1", "r") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getTagStat("1", "a") == 3 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getTagStat("1", "a") == 3 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getTagStat("2", "a") == 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

