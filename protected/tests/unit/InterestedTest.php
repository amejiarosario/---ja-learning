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
		//$this->assertFalse(DEBUG);
		$this->assertTrue(DEBUG);
	}
	
	function testDrupalTutLinks()
	{
		$w = new WebCrawler("http://drupal.org/documentation"); 
		$chap = $w->getSubLinks();
		d(__LINE__,__FILE__, $chap, '$chap');
		
		// assertions
		//$this->assertTrue(count($chap) > 10);
		
		$this->assertEquals($chap[0]['text'],'Understanding Drupal');
		$this->assertEquals($chap[6]['link'],'/documentation/git');
		// avoid link repetition. E.g Installation Guide is repeated
		$this->assertEquals(count($chap),7);
		
		
	}
	
//*/	
}

?>
