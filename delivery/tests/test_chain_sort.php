<?php require 'header.php'?>
This page is for testing the chain sorting
<p>
<script>
var chain = [
	{tag_id: "one - 1.50", tier: 5, adjusted_value: 1.50, count: 0},
	{tag_id: "two - 1.25", tier: 5, adjusted_value: 1.25, count: 0},
	{tag_id: "three - 1.00", tier: 5, adjusted_value: 1.00, count: 0}
];
var chain2 = [
	{tag_id: "one - 1.00", tier: 5, adjusted_value: 1.00, count: 0},
	{tag_id: "two - 0.90", tier: 5, adjusted_value: 0.90, count: 0},
	{tag_id: "three - 0.80", tier: 5, adjusted_value: 0.80, count: 0}
];
for (var i = 0; i < 10000; i++){
	chain.sort(Liftium.chainSort);
	chain[0].count++;

	chain2.sort(Liftium.chainSort);
	chain2[0].count++;
}
document.write("Chain one:  <pre>" + Liftium.print_r(chain) + "</pre>");
document.write("<p>Chain two:  <pre>" + Liftium.print_r(chain2) + "</pre>");
</script>
<script>
var somethingGood = true;
if(somethingGood) {
	LiftiumTest.testPassed();
} else {
	LiftiumTest.testFailed();
}
</script>

<?php require 'footer.php'?>

