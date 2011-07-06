<?php

require_once('../models/WebCrawler.php');

class TutorialWebCrawlerTest extends CTestCase 
{
	function testTester()
	{
		$this->assertEquals(1,1);
		$this->assertSame(true,true);
	}
	
	function testGetUrlElements()
	{
		$url1 = "http://gskinner.com/RegExr/"; //with http, path and no-file
		$url2 = "www.spaweditor.com/scripts/regex/index.php"; // non-http, path and file
		$url3 = "regexpal.com"; // non-http, non-www, non-path or file
		$url4 = "/scripts/regex/"; // no domain, just path
		$url5 = ""; //nothing
		
		$elem1 = getUrlElements($url1);
		$this->assertEquals("gskinner.com", $elem1['domain']);
		$this->assertEquals("/RegExr/", $elem1['path']);
		
		$elem2 = getUrlElements($url2);
		$this->assertEquals("www.spaweditor.com", $elem2['domain']);
		$this->assertEquals("/scripts/regex/index.php", $elem2['path']);
		
		$elem3 = getUrlElements($url3);
		$this->assertEquals("regexpal.com", $elem3['domain']);
		$this->assertEquals("", $elem3['path']);
		
		$elem4 = getUrlElements($url4);
		$this->assertEquals("", $elem4['domain']);
		$this->assertEquals("/scripts/regex/", $elem4['path']);
		
		$elem5 = getUrlElements($url5);
		$this->assertEquals("", $elem5['domain']);
		$this->assertEquals("", $elem5['path']);		
	}
	
	function testGetATags()
	{
		
	}
	
	function testGetTutChapters()
	{
		$exp_chap = array(
			0 => array(
					'href' => 'http://www.adrianmejiarosario.com/doc/guide/1.1/en/changes',
					'text' => 'New Features',
				),
			1 => array(
					// TODO
					'href' => 'http://www.adrianmejiarosario.com/doc/guide/1.1/en/changes',
					'text' => 'New Features',
				),
		);
		
		$website = "http://www.adrianmejiarosario.com/example.html";
		
		//$act_chap = getTutChapters($website);
		//$this->assertSame($exp_chap, $act_chap);
	}
	

}

?>