<?php

require_once('../models/WebCrawler.php');

class TutorialWebCrawlerTest extends CTestCase 
{
	function testFailed()
	{
		//$this->assertEquals(true,false);
		$this->assertEquals(true,true);
	}
	
	function testSuccess()
	{
		$this->assertEquals(1,1);
	}
	
	function testGetTutChapters()
	{
		$website = "http://www.adrianmejiarosario.com/example.html";
		$exp_chap = array(
			0 => array(
					'href' => 'http://www.adrianmejiarosario.com/doc/guide/1.1/en/changes',
					'text' => 'New Features',
				),
			1 => array(
					'href' => 'http://www.adrianmejiarosario.com/doc/guide/1.1/en/changes',
					'text' => 'New Features',
				),
		);
		
		$act_chap = getTutChapters($website);
		$this->assertSame($exp_chap, $act_chap);
	}
	

}

?>