<?php

require_once('../models/WebCrawler.php');

define('TEST_WEBSITE','http://stella.se.rit.edu/tests/index.html');
define('TEST_WEBSITE_LINKS','11');

// asserts -> http://www.phpunit.de/manual/3.2/en/api.html#api.assert.tables.assertions

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
		$this->assertEquals("gskinner.com", $w->getHost());
		$this->assertEquals("/RegExr/", $w->getPath());
		
		// TODO fix this. the path is '/scripts/regex/' and file is 'index.php'
		$w->setHref($url2);
		$this->assertEquals("www.spaweditor.com", $w->getHost());
		$this->assertEquals("/scripts/regex/index.php", $w->getPath()); 
		
		$w->setHref($url3);
		$this->assertEquals("regexpal.com", $w->getHost());
		$this->assertEquals("", $w->getPath());
		
		$w->setHref($url4);
		$this->assertEquals("", $w->getHost());
		$this->assertEquals("/scripts/regex/", $w->getPath());
		
		$w->setHref($url5);
		$this->assertEquals("", $w->getHost());
		$this->assertEquals("", $w->getPath());
		
		// Second test battery
		
		$url = $w->getUrlElements("");
		$this->assertEquals("", $url[1][0]); // schema / protocol
		$this->assertEquals("", $url[2][0]); // domain
		$this->assertEquals("", $url[3][0]); // path
		
		$url = $w->getUrlElements("ftp://www.adrian-mejia.com/index.php");
		$this->assertEquals("ftp", $url[1][0]); // schema / protocol
		$this->assertEquals("www.adrian-mejia.com", $url[2][0]); // domain
		$this->assertEquals("/index.php", $url[3][0]); // path		
		
		
	}
	
	function testGetATags()
	{
		$w = new WebCrawler(TEST_WEBSITE);
		$this->assertEquals($w->getHref(),TEST_WEBSITE);
		
		$atags = $w->getATags();
		$this->assertEquals(TEST_WEBSITE_LINKS, count($atags[0]));
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
	
	function testGetATagsWithSubLinks()
	{
		$w = new WebCrawler(TEST_WEBSITE);
		$chap = $w->getATagsWithSubLinks();
		$this->assertEquals(2, count($chap));
	}
	
	
	// Final test: remove, the final test was the previous one.
	function testGetSubLinks()
	{
		$w = new WebCrawler("http://www.yiiframework.com/doc/guide/");
		$chap = $w->getATagsWithSubLinks();
		var_export($chap);
		
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($chap));
		
		$this->assertTrue(count($chap)>20);
		$this->assertContains("/doc/guide/", $it);
		$this->assertContains("/doc/guide/1.1/en/changes", $chap);
	}
}

?>
