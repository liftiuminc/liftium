<?php $LiftiumOptions = array("offline" => true) ?>
<?php require 'header.php'?>

This page is for testing Liftium.getPropertyCount
<p>
<script>
Liftium.getPropertyCount(null) === 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getPropertyCount(undefined) === 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getPropertyCount(false) === 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getPropertyCount("") === 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getPropertyCount({}) === 0 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getPropertyCount([1,2,3]) === 3 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
Liftium.getPropertyCount({"foo":"bar"}) === 1 ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

