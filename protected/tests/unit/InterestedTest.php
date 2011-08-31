<?php

require_once('../models/WebCrawler.php');
require_once('../models/Utils.php');

define('TEST_WEBSITE','http://stella.se.rit.edu/tests/index.html');
define('TEST_WEBSITE_LINKS','12');

// asserts -> http://www.phpunit.de/manual/3.2/en/api.html#api.assert.tables.assertions

class InterestedTest extends CTestCase 
{

	function testGetATagsWithSubLinks()
	{
		$w = new WebCrawler(TEST_WEBSITE);
		$chap = $w->getSubLinks();
		/*
		d(__LINE__,$w->getHref(),'$w->getHref');
		d(__LINE__,$chap,'$links');
		*/
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($chap));
		$this->assertContains("http://stella.se.rit.edu/tests/index.html", $it);
		$this->assertContains("/tests/index.html",$it);
		$this->assertContains("/tests/path/to/index.html",$it);
		
		$this->assertEquals(3, count($chap));
				
		
		$sampleHTML =<<<HTML
<a href="http://www.adrian.com/test/">true</a>
<a href="/test/">true</a>
<a href="/test/index.php">true</a>
<a href="/test/path/to/index.php">true</a>
<a href="/test/path/to/">true</a>
<a href="#">true</a>
<a href="http://www.google.com/test/">false</a>
<a href="http://www.google.com/test/index.html">false</a>
<a href="http://www.google.com/test/path/to/index.html">false</a>
HTML;
		
		$w->setHref("http://www.adrian.com/test/");
		$links = $w->getSubLinks($sampleHTML);
		
		/*
		d(__LINE__,$w->getHref(),'$w->getHref');
		d(__LINE__,$links,'$links');
		*/
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($links));
		$this->assertContains("/test/",$it);
		$this->assertContains("/test/index.php",$it);
		$this->assertContains("/test/path/to/",$it);
		// TODO add more assertions
		
		$w->setHref("http://www.adrian.com");
		
		$links = $w->getATags($sampleHTML);
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($links));
		
		$this->assertContains("/test/",$it);
		$this->assertContains("/test/index.php",$it);
		$this->assertContains("/test/path/to/",$it);		
		
		$links = $w->getSubLinks($sampleHTML);
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($links));

		//d(__LINE__,__FILE__,$w->getHref(),'$w->getHref');
		//d(__LINE__,__FILE__,$links,'$links');
		
		$this->assertEquals($w->getHref(),"http://www.adrian.com");
		$this->assertContains("/test/",$it);
		$this->assertContains("/test/index.php",$it);
		$this->assertContains("/test/path/to/",$it);
		// TODO add more assertions		
	}
	
}

?>
