<?php
/*
	@see http://gskinner.com/RegExr/ (coolest!)
	
	@see http://www.spaweditor.com/scripts/regex/index.php  (full PHP)
	@seealso http://www.regexpal.com/
*/

/**
 * Get the source code of a website and analize its components.
 */
class WebCrawler {
	private $_href;
	private $_hostname;
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
		unset($this->_hostname);
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
	public function getHost()
	{
		if(!isset($this->_hostname))
		{
			$this->setUrlElements();
		}
		return $this->_hostname;
	}
	
	/**
	 * Get URL path
	 */		
	public function getPath()
	{
		if(!isset($this->_path))
		{
			$this->setUrlElements();
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
	 *
			e.g.[0] => Array ([0] => <a href="/doc/guide/1.1/en/changes">New Features</a>)
			    [1] => Array ([0] => /doc/guide/1.1/en/changes)
			    [2] => Array ([0] => New Features)
	 * DONE handle (whitespaces) <a href = "http://www.adrianmejiarosario.com/tests2/" > fine </ a>
	 */
	public function getATags($HtmlCode='')
	{
		return $this->regex('/<a\\s+href\\s*=\\s*(?:"|\')([^"\']*)[^>]*\\s*>((?:(?!<.a>).)*)<.a>/i', $HtmlCode);
 
	}
	
	public function getUrlElements($url)
	{
		$e = parse_url($url);
		if(!isset($e['host']))
			$e['host'] = '';
		if(!isset($e['path']))
			$e['path'] = '';
	}
	
	public function getATagsWithSubLinks()
	{
		$subLinks = array();
		
		// 1. get all the A Tags
		$aTags = $this->getATags();
		
		// 2. return only the ones that are in the same domain+path or deeper
		for($x=0; $x < count($aTags[1]); $x++)
		{
			$linkUrl = $this->getUrlElements($aTags[1][$x]);
			
			// check that the links are in the tut's path or deeper
			if ( $this->getHost() === $linkUrl['host'] && 
				strpos($this->getPath(), $linkUrl['path']) > -1 ) 
			{
				$subLinks[] = array('name'=>$aTags[2][$x], 'link'=> $aTags[1][$x]);
			}
		}
		
		return $subLinks;
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
		$this->_hostname = $url['host'];
		$this->_path = $url['path'];		
	}
	
}

?>
