<?php
/*
	@see http://gskinner.com/RegExr/ (coolest!)
	
	@see http://www.spaweditor.com/scripts/regex/index.php  (full PHP)
	@seealso http://www.regexpal.com/
*/

/**
 * Print debug information
 * EXAMPLE:
 * 		d(__LINE__,$this->_href,'$this->_href');	
 */
function d($line,$var, $varname="variable")
{
  echo "\n#($line) ". $varname . ' = ';
  var_export($var);
  echo "\n";
}
/**
 * Get the source code of a website and analize its components.
 */
class WebCrawler {
	private $_href;
	private $_domain;
	private $_path;
	private $_sourceCode;
	
	//-----------------
	// Public methods
	//-----------------
	
	/**
	 * Constructor get the link
	 */
	public function __construct($href)
	{
		$this->_href = $href;
	}

	/**
	 * Set link
	 */
	public function setHref($href)
	{
		$this->_href = $href;
		unset($this->_domain);
		unset($this->_path);
		unset($this->_sourceCode);
	}

	/**
	 * Get link
	 */	
	public function getHref()
	{
		return $this->_href;
	}

	/**
	 * Get URL domain
	 */	
	public function getDomain()
	{
		if(!isset($this->_domain))
		{
			$this->setUrlElements();
		}
		return $this->_domain;
	}
	
	/**
	 * Get URL path
	 */		
	public function getPath()
	{
		if(!isset($this->_path))
		{
			setUrlElements();
		}	
		return $this->_path;
	}
	
	/**
	 * Get website source code (content)
	 */		
	public function getSourceCode()
	{
		if(!isset($this->_sourceCode))
		{
			$this->_sourceCode = stream_context_create(array('http' => array('header'=>'Connection: close'))); 
			$this->_sourceCode = file_get_contents($this->getHref()); // TODO handle file not found
		}
		return $this->_sourceCode;
	}
	
	// TODO replace all other preg_match call with this!
	public function regex($regex, $text='')
	{
		if(!isset($regex) || strlen($regex)<3)
			throw new Exception('No regex string to evaluate.');
		if(!isset($text) || strlen($text)<1)
			$text = $this->getSourceCode();	
		// espape values --> replace '\' for '\\'; ''' for '\''; and '/' for '.'	
		preg_match_all($regex, $text, $result);
		return $result;
	}
	
	/**
	 * @return a multidimentional array with the complete a tag [0], links [1] and text [2]. 
			e.g.[0] => Array ([0] => <a href="/doc/guide/1.1/en/changes">New Features</a>)
			    [1] => Array ([0] => /doc/guide/1.1/en/changes)
			    [2] => Array ([0] => New Features)
	 * DONE handle (whitespaces) <a href = "http://www.adrianmejiarosario.com/tests2/" > fine </ a>
	 */
	public function getATags($HtmlCode='')
	{
		return $this->regex('/<a\\s+href\\s*=\\s*(?:"|\')([^"\']*)[^>]*\\s*>((?:(?!<.a>).)*)<.a>/i', $HtmlCode);
 
	}
	public function getATagsWithSubLinks()
	{
	   /*
		* partial links. All of these are in the same domain. (FINE) e.g 
				<a href="/docs/guides/">fine</a>
		* full links in the same domain+path (PARSE) e.g. 
				<a href="http://www.adrianmejiarosario.com/tests2/">fine</a>
				<a href="http://www.adrianmejiarosario.com/tests2/index.html#here">fine</a>
		* full links in other paths or domains (PARSE)
				<a href="http://www.adrianmejiarosario.com/tests/index.html">not</a>
				<a href="www.adrianmejiarosario.com/tests/index.html">not</a>
				<a href="adrianmejiarosario.com/tests/index.html">not</a>
				<a href="gogole.com/tests2">not</a>
		*/
		
		// 1. get all the A Tags
		$aTags = getATags();
		
		// 2. return only the ones that are in the same domain+path or deeper
		foreach($aTags[1] as $linkUrl)
		{
			$linkUrl = getUrlElements($aTag);
			
			// check that the links are in the tut's path or deeper
			if ( $tutUrl['domain'] == $linkUrl['domain'] && 
				strpos($tutUrl['path'], $linkUrl['path']) > -1 ) 
			{
				$chapters[] = array('name'=>$aTagElem['name'], 'link'=> $aTagElem['link']);
			}
		}
		
		//preg_match_all('//i',$HtmlCode,$aTags);
		return $aTags;
	}

	//-----------------
	// Private methods
	//-----------------
	
	/**
	 * Identifies the domain and path of the given URL
	 */
	private function setUrlElements()
	{
		$url = $this->getUrlElements($this->_href);
		$this->_domain = $url['domain'];
		$this->_path = $url['path'];		
	}
}

//------- Main ---------
function main()
{
  echo "Tester v.3\n\n";
  $tut = "http://www.yiiframework.com/doc/guide/";

  $chapters = getTutChapters($tut);
  print_r($chapters);
}

//main();


	/**
	 * @return an array with title and link of the chapters in the Tutorial
	 */
	function getTutChapters($websiteLink)
	{
		$chapters = array();

		// 1. load website in memory
		$webSourceCode = file_get_contents($websiteLink);
		
		// get domain name and path of the website URL
		$tutUrl = getUrlElements($websiteLink);

		// 2. identify all the links in the website. e.g. <a href="/doc/guide/">Guide</a>
		$aTags = getATags($webSourceCode);

		d(__LINE__,$aTags,'$links');

		// 3. Store only the ones that are in the same path or deeper.
		foreach($aTags[0] as $aTag)
		{
			$linkUrl = parse_url($aTag);
			
			//------
			d(__LINE__,$aTag,'$aTag');
			d(__LINE__,$linkUrl,'$linkUrl');
			d(__LINE__,$aTagElem,'$aTagElem');
			//------*/

			// check that the links are in the tut's path or deeper
			if ( $tutUrl['domain'] == $linkUrl['domain'] && 
				strpos($tutUrl['path'], $linkUrl['path']) > -1 ) 
			{
				$chapters[] = array('name'=>$aTagElem['name'], 'link'=> $aTagElem['link']);
			}
		}

		return $chapters;
	}

	
		function getATagElements($aTag)
	{
		$elements = array();
		preg_match('/(?<=href\=")[^]+?(?=")/',$link,$match);
		$elements['link'] = $match[0];
		preg_match('/(?<=^|>)[^><]+?(?=<|$)/',$link,$match);
		$elements['name'] = $match[0];
		return $elements;
	}

	function getATags($webSourceCode)
	{
		preg_match_all('/<a href="[^"]+"[^>]*>[^<]+<\/a>/i',$webSourceCode, $aTags);
		return $aTags;
	}

?>
