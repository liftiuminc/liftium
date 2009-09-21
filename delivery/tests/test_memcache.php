<?php require dirname(__FILE__) . '/../../includes/LiftiumTest.php'; ?>
<?php require 'header.php'?>
This page is for testing memcache.
<p>
<?php
// Load the framework.
require 'LiftiumCache.php';

define('CACHE_KEY', 'test_memcache_key_' . uniqid(rand()));

try {
	// Test instantialion of the LiftiumCache class.
	try {
		$c = LiftiumCache::getInstance();
		LiftiumTest::testPassed('LiftiumCache::getInstance()');
	} catch (Exception $e){
		print "Unable to instantiate LiftiumCache: " . $e->getMessage();
		LiftiumTest::testFailed('LiftiumCache::getInstance()');
		throw new StopTest();
	}
	
	// Try getting the missing cache key. Should return false.
	//try {
		$ret = $c->get(CACHE_KEY);
		LiftiumTest::testCondition($ret === false, 'get missing key === false : ' . var_export($ret, true));
	//} catch (Exception $e) {
	//}
	
	// Test incrementing the missing key without a value. Should return 0. FIXME: The documented definition of this has changed from current behavior? -Martel DuVigneaud 2009-09-21
	//try {
		$ret = $c->increment(CACHE_KEY);
		LiftiumTest::testCondition($ret === false || $ret === 0, 'increment missing key === 0 : ' . var_export($ret, true));
	//} catch (Exception $e) {
	//}
	
	// Test setting the key. Should return null. // FIXME: The documented definition of this has changed from current behavior? -Martel DuVigneaud 2009-09-21
	//try {
		$ret = $c->set(CACHE_KEY, 1);
		LiftiumTest::testCondition($ret === null, 'set key (to 1) === NULL : ' . var_export($ret, true));
	//} catch (Exception $e) {
	//}
	
	// Test incrementing the key. Should return 2.
	//try {
		$ret = $c->increment(CACHE_KEY);
		LiftiumTest::testCondition($ret === 2, 'increment key (to 2) : ' . var_export($ret, true));
	//} catch (Exception $e){
	//}
	
	// Test getting the key. Should return '2'.
	//try {
		$ret = $c->get(CACHE_KEY);
		LiftiumTest::testCondition($ret === '2', 'get key === \'2\' : ' . var_export($ret, true));
	//} else {
	//}
	
	// Test incrementing key by value. Should return 4.
	//try {
		$ret = $c->increment(CACHE_KEY, 2);
		LiftiumTest::testCondition($ret === 4, 'increment key (by 2) === 4 : ' . var_export($ret, true));
	//} catch (Exception $e) {
	//}
} catch (StopTest $s) {
	// Nothing to do, this is just an exception used to abort the testing early.
} catch (Exception $e){
	LiftiumTest::testFailed('unknown exception: ' . $e->getMessage());
}

?>
<script>
//Liftium.empty(false) ? LiftiumTest.testPassed() : LiftiumTest.testFailed();
</script>

<?php require 'footer.php'?>

