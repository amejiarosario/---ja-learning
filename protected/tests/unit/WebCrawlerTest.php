<?php

require_once('../models/WebCrawler.php');
define('TEST_WEBSITE','http://stella.se.rit.edu/tests/index.html');

class WebCrawlerTest extends CTestCase 
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
		
		$w = new WebCrawler($url1);
		
		$this->assertEquals($url1, $w->getHref());
		$this->assertEquals("gskinner.com", $w->getDomain());
		$this->assertEquals("/RegExr/", $w->getPath());
		
		// TODO fix this. the path is '/scripts/regex/' and file is 'index.php'
		$w->setHref($url2);
		$this->assertEquals("www.spaweditor.com", $w->getDomain());
		$this->assertEquals("/scripts/regex/index.php", $w->getPath()); 
		
		$w->setHref($url3);
		$this->assertEquals("regexpal.com", $w->getDomain());
		$this->assertEquals("", $w->getPath());
		
		$w->setHref($url4);
		$this->assertEquals("", $w->getDomain());
		$this->assertEquals("/scripts/regex/", $w->getPath());
		
		$w->setHref($url5);
		$this->assertEquals("", $w->getDomain());
		$this->assertEquals("", $w->getPath());
		
		/* Extended tests
		
		$w->setHref('adrian.com/test/index.php#here');
		$this->assertEquals("adrian.com", $w->getDomain());
		$this->assertEquals("/test/", $w->getPath()); 
		
		// @see http://docstore.mik.ua/orelly/linux/cgi/ch02_01.htm
		$w->setHref('http://www.adrianmejiarosario.com:80/cgi/calendar.cgi?month=july#week3');
		//*/
	}
	
	function testGetATags()
	{
		$w = new WebCrawler(TEST_WEBSITE);
		$this->assertEquals($w->getHref(),TEST_WEBSITE);
		
		$atags = $w->getATags();
		$this->assertEquals(9, count($atags[0]));
		$this->assertEquals('<a href="/doc/guide/1.1/en/changes">New Features</a>', $atags[0][0]);
		$this->assertEquals('/doc/guide/1.1/en/changes', $atags[1][0]);
		$this->assertEquals('New Features', $atags[2][0]);
		
		$htmlCode = <<<HTML
<a href="http://twitter.com/?status=http%3A//www.adrianmejiarosario.com/content/drupal-modules-seo-optimation%20Drupal%20Modules%20for%20SEO%20optimation%20" class="tweet" rel="nofollow" onclick="window.open(this.href); return false;"><img typeof="foaf:Image" src="http://www.adrianmejiarosario.com/sites/all/modules/tweet/twitter.png" alt="Post to Twitter" title="Post to Twitter" /></a>
HTML;
		$atags = $w->getATags($htmlCode);
		$this->assertEquals(1, count($atags[0]));
		$this->assertEquals($htmlCode, $atags[0][0]);
		$this->assertEquals('http://twitter.com/?status=http%3A//www.adrianmejiarosario.com/content/drupal-modules-seo-optimation%20Drupal%20Modules%20for%20SEO%20optimation%20', $atags[1][0]);
		$this->assertEquals('<img typeof="foaf:Image" src="http://www.adrianmejiarosario.com/sites/all/modules/tweet/twitter.png" alt="Post to Twitter" title="Post to Twitter" />', $atags[2][0]);
			
	}
	
	
	// Final test
	function testGetSubLinks()
	{
		$exp_chap = array(
			0 => array(
					'href' => 'http://www.adrianmejiarosario.com/doc/guide/1.1/en/changes',
					'text' => 'New Features',
				),
			1 => array(
					'href' => 'http://www.adrianmejiarosario.com/doc/guide/1.1/en/upgrade',
					'text' => 'Upgrading from 1.0 to 1.1',
				),
			2 => array(
					'href' => 'http://www.adrianmejiarosario.com/doc/guide/1.1/en/quickstart.what-is-yii',
					'text' => 'What is Yii',
				),				
		);
		
		$w = new WebCrawler(TEST_WEBSITE);
		
		//$act_chap = $w->getSubLinks();
		//$this->assertSame($exp_chap, $act_chap);
	}
	

}

?>
