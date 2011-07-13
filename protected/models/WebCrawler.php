<?php

require_once('Utils.php');

/**
 * Get the source code of a website and analize its components.
 */
class WebCrawler {
	private $_href;
	private $_hostname;
	private $_path;
	private $_file;
	private $_query;
	private $_sourceCode;
	
	/**
	 * Constructor 
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
		unset($this->_file);
		unset($this->_query);
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
	public function getDomain() {return getHost();}
	
	/**
	 * Get File	 
	 */	
	public function getFile()
	{
		if(!isset($this->_file))
		{
			$this->setUrlElements();
		}
		return $this->_file;
	}
	
	/**
	 * Get Query	 
	 */	
	public function getQuery()
	{
		if(!isset($this->_query))
		{
			$this->setUrlElements();
		}
		return $this->_query;
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
			$this->_sourceCode = file_get_contents($this->getHref()); // TODO handle file not found
		}
		return $this->_sourceCode;
	}
	
	/**
	 * Regular expression evaluator
	 * @param $regex regular expression
	 * @param $text text to apply the regex
	 * @return multi array of results from the evaluation of the regular expression (RegEx)
	 *
	 */
	public function regex($regex, $text='')
	{
		if(!isset($regex) || strlen($regex)<3)
			throw new Exception('No regex string to evaluate.');
		// espape values --> replace '\' for '\\' OR '/' for '.'; ''' for '\'';
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
		if(!isset($HtmlCode) || strlen($HtmlCode)<1)
			$HtmlCode = $this->getSourceCode();	
		$a = $this->regex('%<a\\s+href\\s*=\\s*(?:"|\')([^"\']*)[^>]*\\s*>((?:(?!</a>).)*)</a>%i', $HtmlCode);
		return array('ahref'=>$a[0],'link'=>$a[1],'text'=>$a[2]);
 
	}
	
	/**
	 * Parse the URL address into its parts.
	 * @return multi-dimensional array: 
	 *		[0] link (complete match)
	 * 		[1] schema (http,ftp,...); 
	 * 		[2] domain/host; 
	 *		[3] path 
	 *		[4] file
	 *		[5] query
	 */
	public function getUrlElements($url)
	{
		// get file and query
		// (\w+\.\w+)([^.]*)$
		
		
		$arr = $this->regex('%^(?:(https?|ftp|file)://)?([a-z0-9-]+(?:\.[a-z0-9-]+)+)?(.*?)?(?:(\w+\.\w+)([^.]*))?$%i', $url);
		return array('link' => $arr[0], 'schema'=>$arr[1], 'domain'=>$arr[2], 'path'=>$arr[3], 'file'=>$arr[4], 'query'=>$arr[5]);
	}
	
	/**
	 * Identifies the domain and path of the given URL
	 */
	private function setUrlElements()
	{
		$url = $this->getUrlElements($this->_href);
		$this->_hostname = $url['domain'][0];
		$this->_path = $url['path'][0];
		$this->_file = $url['file'][0];
		$this->_query = $url['query'][0];	
	}

	/**
	 * @return an array with the keys 'name' and 'links' of the sublinks
	 */
	public function getATagsWithSubLinks($HtmlCode = '')
	{
		$subLinks = array();
		
		// 1. get all the A Tags
		$aTags = $this->getATags($HtmlCode);
		
		// 2. return only the ones that are in the same domain+path or deeper
		for($x=0; $x < count($aTags['link']); $x++)
		{
			$linkUrl = $this->getUrlElements($aTags['link'][$x]);
			$equivalent_path = "";
					
			if($this->getPath() == "")
				$equivalent_path = "/";
			else
				$equivalent_path = $this->getPath();

			
			$samePath = false;
			
			// if domains are equals
			if($this->getHost() === $linkUrl['domain'][0] || $linkUrl['domain'][0] === "" ) 
			{
				// if there is not path in the domain, all the links' path are inside 
				if(	$this->getPath() === "" || 
					$this->getPath() === "/" || 
					strpos($linkUrl['path'][0],$this->getPath()) === 0
					)  
				{	
					$subLinks[] = array('name'=>$aTags['text'][$x], 'link'=> $aTags['link'][$x]);
				} 
			}
			
			/*
			if($this->getPath() === $linkUrl['path'][0] ) // equal path
				$samePath = true;
			else if($this->getPath() === "" || $linkUrl['path'][0] === "")
				$samePath = false;
			else if(strpos($linkUrl['path'][0],$equivalent_path) > -1) // deeper
				$samePath = true;

			// check that the links are in the tut's path or deeper
			if ( $samePath && ( $linkUrl['domain'][0]==="" || ($this->getHost() === $linkUrl['domain'][0])) ) 
			{
				
			}
			*/
		}
		
		return $subLinks;
	}
}

?>
