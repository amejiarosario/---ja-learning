<?php

require_once('../models/WebCrawler.php');
require_once('../models/Utils.php');

define('TEST_WEBSITE','http://stella.se.rit.edu/tests/index.html');
define('TEST_WEBSITE_LINKS','12');

// asserts -> http://www.phpunit.de/manual/3.2/en/api.html#api.assert.tables.assertions

class InterestedTest extends CTestCase 
{
//*
	function testDebug()
	{
		$this->assertFalse(DEBUG);
	}
//*/	
}

?>
