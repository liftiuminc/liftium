<?php require 'header.php'?>
This page is for testing Lift.empty.
<p>
<script>
// Truthies
Lift.empty(false) ? LiftTest.testPassed() : LiftTest.testFailed();
Lift.empty(parseInt("blah")) ? LiftTest.testPassed() : LiftTest.testFailed(); 
Lift.empty(parseFloat("blah")) ? LiftTest.testPassed() : LiftTest.testFailed();
Lift.empty("") ? LiftTest.testPassed() : LiftTest.testFailed();
Lift.empty(null) ? LiftTest.testPassed() : LiftTest.testFailed();
Lift.empty({}) ? LiftTest.testPassed() : LiftTest.testFailed();
Lift.empty([]) ? LiftTest.testPassed() : LiftTest.testFailed();
Lift.empty(0) ? LiftTest.testPassed() : LiftTest.testFailed();

// Falsies
!Lift.empty(true) ? LiftTest.testPassed() : LiftTest.testFailed();
!Lift.empty(parseInt("23")) ? LiftTest.testPassed() : LiftTest.testFailed();
!Lift.empty(parseFloat("3.14159")) ? LiftTest.testPassed() : LiftTest.testFailed();
!Lift.empty("string") ? LiftTest.testPassed() : LiftTest.testFailed();
!Lift.empty(Array(1,2,3)) ? LiftTest.testPassed() : LiftTest.testFailed();
!Lift.empty({"foo":"bar"}) ? LiftTest.testPassed() : LiftTest.testFailed();
!Lift.empty("0") ? LiftTest.testPassed() : LiftTest.testFailed();
!Lift.empty(Lift) ? LiftTest.testPassed() : LiftTest.testFailed();
</script>

<?php require 'footer.php'?>

