<?php

// Setup the include path.
set_include_path(dirname(realpath(__FILE__)) . PATH_SEPARATOR . get_include_path());

require 'Framework.php';

// Abort the testing.
class StopTest extends Exception {
	// Not a whole lot to do.
}

class LiftiumTest {
	public static function testCondition ($condition, $message = null) {
		if ($condition) {
			return self::testPassed($message);
		} else {
			return self::testFailed($message);
		}
	}
	public static function testPassed ($message = null){
		if ($message !== null){
			print 'Passed: ' . $message . '<br />';
		}
		print '<script>LiftiumTest.testPassed()</script>';
		return true;
	}
	public static function testFailed ($message = null){
		if ($message !== null){
			print 'Failed: ' . $message . '<br />';
		}
		print '<script>LiftiumTest.testFailed()</script>';
		return false;
	}
	public function __construct (){
		throw new Exception(__CLASS__ . ' cannot be instantiated.');
	}
}

?>
