<?php

require_once('../models/WebCrawler.php');
require_once('../models/Utils.php');

define('TEST_WEBSITE','http://stella.se.rit.edu/tests/index.html');
define('TEST_WEBSITE_LINKS','12');

// asserts -> http://www.phpunit.de/manual/3.2/en/api.html#api.assert.tables.assertions

class InterestedTest extends CTestCase 
{

	function testOddTutLinks()
	{
		$w = new WebCrawler('http://www.go2linux.org/latex-simple-tutorial');
		
		// third battery test
		//http://www.go2linux.org/latex-simple-tutorial
		$this->assertEquals("www.go2linux.org", $w->getHost());
		$this->assertEquals("/", $w->getPath());
		$this->assertEquals("latex-simple-tutorial", $w->getFile());
		$this->assertEquals("", $w->getQuery());
		//http://library.rit.edu/libhours
		//http://docs.python.org/tutorial/appetite.html	
	}
	
/*
	function testGetATagsWithSubLinksRealTut()
	{
		$w = new WebCrawler('http://www.go2linux.org/latex-simple-tutorial');
		$chaps = $w->getChapters();
		d(__LINE__,__FILE__,$chaps,'$chaps');
		
	}
//*/	
}

?>
