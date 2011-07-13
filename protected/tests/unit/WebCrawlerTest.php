<?php

require_once('../models/WebCrawler.php');
require_once('../models/Utils.php');

define('TEST_WEBSITE','http://stella.se.rit.edu/tests/index.html');
define('TEST_WEBSITE_LINKS','12');

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
		//*
		$this->assertEquals($url1, $w->getHref());
		$this->assertEquals("gskinner.com", $w->getHost());
		$this->assertEquals("/RegExr/", $w->getPath());
		
		// TODO fix this. the path is '/scripts/regex/' and file is 'index.php'
		$w->setHref($url2);
		$this->assertEquals("www.spaweditor.com", $w->getHost());
		$this->assertEquals("/scripts/regex/", $w->getPath()); 
		$this->assertEquals("index.php", $w->getFile());
		
		$w->setHref($url3);
		$this->assertEquals("regexpal.com", $w->getHost());
		$this->assertEquals("", $w->getPath());
		$this->assertEquals("", $w->getFile());
		
		$w->setHref($url4);
		$this->assertEquals("", $w->getHost());
		$this->assertEquals("/scripts/regex/", $w->getPath());
		
		$w->setHref($url5);
		$this->assertEquals("", $w->getHost());
		$this->assertEquals("", $w->getPath());
		$this->assertEquals("", $w->getFile());
		
		$w->setHref('ftp://www.adrian-mejia.com/dir/index.php?r=module/site/index#week3');
		$this->assertEquals("www.adrian-mejia.com", $w->getHost());
		$this->assertEquals("/dir/", $w->getPath());
		$this->assertEquals("index.php", $w->getFile());
		$this->assertEquals("?r=module/site/index#week3", $w->getQuery());
		//*/
		// Second test battery
		
		$url = $w->getUrlElements("ftp://www.adrian-mejia.com/dir/index.php?r=module/site/index#week3");
		//d(__LINE__,$url,'$url');
		$this->assertEquals("ftp", $url['schema'][0]); // schema / protocol
		$this->assertEquals("www.adrian-mejia.com", $url['domain'][0]); // domain
		$this->assertEquals("/dir/", $url['path'][0]); // path
		$this->assertEquals("index.php", $url['file'][0]); 
		$this->assertEquals("?r=module/site/index#week3", $url['query'][0]); 	
		
		$url = $w->getUrlElements("");
		$this->assertEquals("", $url['schema'][0]); // schema / protocol
		$this->assertEquals("", $url['domain'][0]); // domain
		$this->assertEquals("", $url['path'][0]); // path
		
		$url = $w->getUrlElements("ftp://www.adrian-mejia.com/index.php");
		$this->assertEquals("ftp", $url['schema'][0]); // schema / protocol
		$this->assertEquals("www.adrian-mejia.com", $url['domain'][0]); // domain
		$this->assertEquals("/", $url['path'][0]); // path		
		
	}
	
	function testGetATags()
	{
		$w = new WebCrawler(TEST_WEBSITE);
		$this->assertEquals($w->getHref(),TEST_WEBSITE);
		
		$atags = $w->getATags();
		$this->assertEquals(TEST_WEBSITE_LINKS, count($atags['ahref']));
		$this->assertEquals('<a href="/doc/guide/1.1/en/changes">New Features</a>', $atags['ahref'][0]);
		$this->assertEquals('/doc/guide/1.1/en/changes', $atags['link'][0]);
		$this->assertEquals('New Features', $atags['text'][0]);
		
		$htmlCode = <<<HTML
<a href="http://twitter.com/?status=http%3A//www.adrianmejiarosario.com/content/drupal-modules-seo-optimation%20Drupal%20Modules%20for%20SEO%20optimation%20" class="tweet" rel="nofollow" onclick="window.open(this.href); return false;"><img typeof="foaf:Image" src="http://www.adrianmejiarosario.com/sites/all/modules/tweet/twitter.png" alt="Post to Twitter" title="Post to Twitter" /></a>
HTML;
		$atags = $w->getATags($htmlCode);
		$this->assertEquals(1, count($atags['ahref']));
		$this->assertEquals($htmlCode, $atags['ahref'][0]);
		$this->assertEquals('http://twitter.com/?status=http%3A//www.adrianmejiarosario.com/content/drupal-modules-seo-optimation%20Drupal%20Modules%20for%20SEO%20optimation%20', $atags['link'][0]);
		$this->assertEquals('<img typeof="foaf:Image" src="http://www.adrianmejiarosario.com/sites/all/modules/tweet/twitter.png" alt="Post to Twitter" title="Post to Twitter" />', $atags['text'][0]);
			
	}
	
	function testGetATagsWithSubLinks()
	{
		$w = new WebCrawler(TEST_WEBSITE);
		$chap = $w->getATagsWithSubLinks();
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
		$links = $w->getATagsWithSubLinks($sampleHTML);
		
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
		
		$links = $w->getATagsWithSubLinks($sampleHTML);
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($links));

		//d(__LINE__,__FILE__,$w->getHref(),'$w->getHref');
		//d(__LINE__,__FILE__,$links,'$links');
		
		$this->assertEquals($w->getHref(),"http://www.adrian.com");
		$this->assertContains("/test/",$it);
		$this->assertContains("/test/index.php",$it);
		$this->assertContains("/test/path/to/",$it);
		// TODO add more assertions		
	}
	
	
	// Final test: remove, the final test was the previous one.
	function testGetSubLinks()
	{
		$w = new WebCrawler("http://www.yiiframework.com/doc/guide/");
		$chap = $w->getATagsWithSubLinks();
		
		//d(__LINE__,__FILE__,$chap,'chap');
		
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($chap));
		
		$this->assertTrue(count($chap) > 20);
		$this->assertContains("/doc/guide/", $it);

		$this->assertContains("/doc/guide/1.1/en/changes", $it);
		$this->assertContains("/doc/guide/1.1/en/quickstart.first-app", $it);
		$this->assertContains("/doc/guide/1.1/sv/index", $it);

		$this->assertNotContains("/doc/api/", $it);
		$this->assertNotContains("/wiki/", $it);
		$this->assertNotContains("/", $it);
		$this->assertNotContains("", $it);
	}
}

?>
