<?php

require_once('../models/WebCrawler.php');
require_once('../models/Utils.php');

define('TEST_WEBSITE','http://www.go2linux.org/latex-simple-tutorial');
define('TEST_WEBSITE_LINKS','26');

// asserts -> http://www.phpunit.de/manual/3.2/en/api.html#api.assert.tables.assertions

class WebCrawlerTest extends CTestCase 
{
	// just testing
	function testTester()
	{
		$this->assertEquals(1,1);
		$this->assertSame(true,true);
	}
	
	// test regex url parser (get url elements)
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
		$this->assertEquals("/", $w->getPath());
		$this->assertEquals("", $w->getFile());
		
		$w->setHref($url4);
		$this->assertEquals("", $w->getHost());
		$this->assertEquals("/scripts/regex/", $w->getPath());
		
		$w->setHref($url5);
		$this->assertEquals("", $w->getHost());
		$this->assertEquals("/", $w->getPath());
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
		$this->assertEquals("ftp://", $url['schema'][0]); // schema / protocol
		$this->assertEquals("www.adrian-mejia.com", $url['domain'][0]); // domain
		$this->assertEquals("/dir/", $url['path'][0]); // path
		$this->assertEquals("index.php", $url['file'][0]); 
		$this->assertEquals("?r=module/site/index#week3", $url['query'][0]); 	
		
		$url = $w->getUrlElements("");
		$this->assertEquals("", $url['schema'][0]); // schema / protocol
		$this->assertEquals("", $url['domain'][0]); // domain
		$this->assertEquals("", $url['path'][0]); // path
		
		$url = $w->getUrlElements("ftp://www.adrian-mejia.com/index.php");
		$this->assertEquals("ftp://", $url['schema'][0]); // schema / protocol
		$this->assertEquals("www.adrian-mejia.com", $url['domain'][0]); // domain
		$this->assertEquals("/", $url['path'][0]); // path		
		
		//http://www.yiiframework.com/doc/guide/1.1/en
		$w->setHref('http://www.yiiframework.com/doc/guide/1.1/en');
		$this->assertEquals("www.yiiframework.com", $w->getHost());
		$this->assertEquals("/doc/guide/1.1/en", $w->getPath());
		$this->assertEquals("", $w->getFile());
		$this->assertEquals("", $w->getQuery());
		
	}
	
	// test the url parser inside websites (find html A tags)
	function testGetATags()
	{
		// testing with URL
		$testurl = "http://stella.se.rit.edu/tests/index.html";
		$testlinks = 13;
		
		$w = new WebCrawler($testurl);
		$this->assertEquals($w->getHref(),$testurl);
		
		$atags = $w->getATags();
		$this->assertEquals($testlinks, count($atags['ahref']));
		$this->assertEquals('<a href="/doc/guide/1.1/en/changes">New Features</a>', $atags['ahref'][0]);
		$this->assertEquals('/doc/guide/1.1/en/changes', $atags['link'][0]);
		$this->assertEquals('New Features', $atags['text'][0]);
		
		// testing with given html code
		$htmlCode = <<<HTML
<a href="http://twitter.com/?status=http%3A//www.adrianmejiarosario.com/content/drupal-modules-seo-optimation%20Drupal%20Modules%20for%20SEO%20optimation%20" class="tweet" rel="nofollow" onclick="window.open(this.href); return false;"><img typeof="foaf:Image" src="http://www.adrianmejiarosario.com/sites/all/modules/tweet/twitter.png" alt="Post to Twitter" title="Post to Twitter" /></a>
HTML;
		$atags = $w->getATags($htmlCode);
		$this->assertEquals(1, count($atags['ahref']));
		$this->assertEquals($htmlCode, $atags['ahref'][0]);
		$this->assertEquals('http://twitter.com/?status=http%3A//www.adrianmejiarosario.com/content/drupal-modules-seo-optimation%20Drupal%20Modules%20for%20SEO%20optimation%20', $atags['link'][0]);
		$this->assertEquals('<img typeof="foaf:Image" src="http://www.adrianmejiarosario.com/sites/all/modules/tweet/twitter.png" alt="Post to Twitter" title="Post to Twitter" />', $atags['text'][0]);

		//-----------------------+		
		// testing with real tut |
		//-----------------------+
		
		$w->setHref(TEST_WEBSITE);
		$atags = $w->getATags();
		
		//d(__LINE__,__FILE__, $atags, '$atags');
		
		$this->assertEquals(TEST_WEBSITE_LINKS, count($atags['ahref']));
		$this->assertEquals('<a href="http://www.go2linux.org/latex-introduction" title="Introduction to LaTeX in Linux">Introduction to LaTeX</a>', $atags['ahref'][1]);
		$this->assertEquals('http://www.go2linux.org/latex-introduction', $atags['link'][1]);
		$this->assertEquals('Introduction to LaTeX', $atags['text'][1]);
			
	}
	
	// get the sublinks (chapters) and their contents!
	function testGetATagsWithSubLinks()
	{
		$w = new WebCrawler("http://stella.se.rit.edu/tests/tutorial");
		$chap = $w->getSubLinks();
		//d(__LINE__,__FILE__,$chap,'$links');
		
		$it = new RecursiveIteratorIterator( new RecursiveArrayIterator($chap));
		$this->assertContains("/tests/tutorial/chap1.html", $it);
		$this->assertContains("/tests/tutorial/chap2.html",$it);
		$this->assertContains("/tests/tutorial/chap3.html",$it);
		$this->assertEquals(3, count($chap));
		
		// check $w->getSubLinks elements
		
		//get the tut's titles
		$this->assertEquals("chap1", $chap[0]['text']);
		$this->assertEquals("chap2", $chap[1]['text']);
		$this->assertEquals("chap3", $chap[2]['text']);
		// get the tut's links
		$this->assertEquals("/tests/tutorial/chap1.html", $chap[0]['link']);
		$this->assertEquals("/tests/tutorial/chap2.html", $chap[1]['link']);
		$this->assertEquals("/tests/tutorial/chap3.html", $chap[2]['link']);	
		// get the tut content
		$this->assertEquals("<body><h1>Chap1</h1><p>this is some content.</p></body>", $chap[0]['content']);
		$this->assertEquals("<body><h2>Chap2</h2><p>this is some content.</p></body>", $chap[1]['content']);
		$this->assertEquals("<body><h3>Chap3</h3><p>this is some content.</p></body>", $chap[2]['content']);				
		
		// test inline code
		
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
		//d(__LINE__,__FILE__,$links,'$links');
		
		/*
		d(__LINE__,$w->getHref(),'$w->getHref');
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
	}
	
	function testTutsWithInvalidLinks()
	{
		$w = new WebCrawler("http://stella.se.rit.edu/tests/");
		$chap = $w->getSublinks();
		d(__LINE__,__FILE__,$chap,'$chap');
	}
	
	// todo load other tests from the dropbox
	
	function testGetATagsWithSubLinksRealTut()
	{
		
	}
        
 	
	
} // END UNIT TESTING CLASS

?>
