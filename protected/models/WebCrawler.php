<?php

require_once('Utils.php');

/**
 * Get the source code of a website and analize its components.
 */
class WebCrawler 
{
	/**
	 * tutorial URL (schema+domain+path)
	 */
	public $root;
	
	private $_href;
	private $_schema;
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
	 * Set website elements
	 */
	public function setHref($href)
	{
		$this->_href = $href;
		unset($this->root);
		unset($this->_hostname);
		unset($this->_schema);
		unset($this->_path);
		unset($this->_sourceCode);
		unset($this->_file);
		unset($this->_query);
	}

	/**
	 * Get link (full)
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
	 * Get File	name (e.g. index.php)
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
	 * Get Query string (e.g. ?pid=31&uid=1	)
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
	 * Identifies the domain and path of the already given URL
	 */
	private function setUrlElements()
	{
		$url = $this->getUrlElements($this->_href);
		$this->_schema = $url['schema'][0];
		$this->_hostname = $url['domain'][0];
		$this->_path = $url['path'][0];
		$this->_file = $url['file'][0];
		$this->_query = $url['query'][0];	

		// add '/' default path if not present
		if($this->_path === "")
			$this->_path = "/";
		
		$this->root = $this->_schema . "://" . $this->_hostname . $this->_path;
	}
	
	/**
	 * Parse the URL address into its parts.
	 * @return multi-dimensional array (all matches): 
	 *		[0] link (complete match)
	 * 		[1] schema (http,ftp,...); 
	 * 		[2] domain/host (www.adrianmejiarosario.com)
	 *		[3] path  (/test/)
	 *		[4] file  (index.php)
	 *		[5] query (?pid=12)
	 */
	public function getUrlElements($url)
	{
		$arr = regex('%^((?:https?|ftp|file)://)?([a-z0-9-]+(?:\.[a-z0-9-]+)+)?(.*?)?(?:(\w+\.\w+)((?:#|\?|$)(?:[^.]*)))?$%i', $url);
		return array('link' => $arr[0], 'schema'=>$arr[1], 'domain'=>$arr[2], 'path'=>$arr[3], 'file'=>$arr[4], 'query'=>$arr[5]);
	}
	
	/**
	 * @url url to extract content
	 * @title block of information to extract. 	
	 * @return page content (text main content)
	 */
	public function getContent($url, $title="")
	{
	
		//echo ' L158 getContent.url = '.$url;
		//validate URL
		$surl = $this->getUrlElements($url);
		if(	empty($surl['schema'][0]) || 
			empty($surl['domain'][0]) 
		)
		{
			$this->setUrlElements();
			$url = $this->_schema.$this->_hostname.$surl['path'][0].$surl['file'][0];
		}
	
		// Load content
		$html = file_get_contents($url);
		//d(__LINE__,__FILE__,$html,'$html');
		
		$dom = new DOMDocument;
		$dom->loadHTML($html);
		//$dom->formatOutput = false;
		$dom->preserveWhiteSpace = false;
		//echo $dom->validate();
		//echo $dom->saveHTML();
		//echo "\n---------------\n";

		// Search chapter title in the content		
		$body = $dom->getElementsByTagName('body')->item(0);
		//WebCrawler::getChildren($body);
		/*
		for( $i=0; $i < $body->length; $i++ )
		{
			echo $body->item($i)->nodeName;
			//echo " = " . $body->item($i)->nodeValue;
			echo "\n";
		}
		*/
		
		// Extract the block where the title is
		
		$new = new DomDocument;
		$new->appendChild($new->importNode($body, true));
		$foo = $new->saveHTML();
		$foo = trim($foo); 
		//$foo = preg_replace( '/\s+/', ' ', $foo );
		return $foo;
		//return trim($new->saveHTML());
		//return str_replace("\n","",$new->saveHTML());
		//return $body->get_content();
	} // end function
	
	public static function getChildren($node)
	{
		echo "\n".get_class($node);
		
		if($node instanceof DOMNodeList)
		{
			for($x = 0; $x < $node->length; $x++)
			{
				WebCrawler::getChildren($node->item($x));
				/*
				$node->item($x)->nodeName;
				$node->item($x)->nodeValue;
				*/
			}
		}
		elseif($node instanceof DOMNode)
		{
			$children = $node->childNodes;
			if($children->length > 0)
			{
				WebCrawler::getChildren($children);
			}
			else
			{
				///*
				echo '<'.$node->nodeName.'>';
				//echo $node->nodeValue;
				echo '</'.$node->nodeName.'>';
				echo "\n";
				//*/			
			}
		}
		else // DOMNode or DOMDocument
		{
			echo "other class";
		}
	}
	
	/**
	  @return a multidimentional array with the complete a tag [0], links [1] and text [2]. 
	 
			e.g.[0] => Array ([0] => <a href="/doc/guide/1.1/en/changes">New Features</a>)
			    [1] => Array ([0] => /doc/guide/1.1/en/changes)
			    [2] => Array ([0] => New Features)
	  DONE handle (whitespaces) <a href = "http://www.adrianmejiarosario.com/tests2/" > fine </ a>
	 */
	public function getATags($HtmlCode='')
	{
		if(!isset($HtmlCode) || strlen($HtmlCode)<1)
			$HtmlCode = $this->getSourceCode();	
		$a = regex('%<a\\s+href\\s*=\\s*(?:"|\')([^"\']*)[^>]*\\s*>((?:(?!</a>).)*)</a>%i', $HtmlCode);
		return array('ahref'=>$a[0],'link'=>$a[1],'text'=>$a[2]);
 	}
	
    /**
	 * @return an array with the keys 'name', 'links', and the 'content' of the sublinks
	 */
	public function getSubLinks($HtmlCode = '')
	{
		$subLinks = array();
		
		// 1. get all the A Tags
		$chapURLs = $this->getATags($HtmlCode);
		
		// TODO-IMHERE: root = schema+domain+path; links= diff(root,link);
		
		// 2. return only the ones that are in the same domain+path or deeper
		for($x=0; $x < count($chapURLs['link']); $x++)
		{
			// all path should with '/'
			//if(strrpos($this->getPath()) === strlen($this->getPath())-1)
			
			// get equivalent link
			if(strpos($chapURLs['link'][$x],".") === 0) // if link starts with '.'  e.g. href="./index.html"
				$chapURLs['link'][$x] = $this->getPath() . $chapURLs['link'][$x]; // subtitute '.' with current path
				
			// remove double slashes
			$chapURLs['link'][$x] = str_replace("/./","/",$chapURLs['link'][$x]); 
			$chapURLs['link'][$x] = str_replace("./","/",$chapURLs['link'][$x]); 
			
			$chapURL = $this->getUrlElements($chapURLs['link'][$x]);
		
			// if domains are equals
			if($this->getHost() === $chapURL['domain'][0] || $chapURL['domain'][0] === "" ) 
			{
				// if there is not path in the domain, all the links' path are inside 
				if(	$this->getPath() === "/" || 
					strpos($chapURL['path'][0],$this->getPath()) === 0 )  
				{
					
					$chapURLs['text'][$x] = strip_tags($chapURLs['text'][$x]); // strip html tags
					
					// if it has some content besides HTML tags save it, otherwise discard it.
					if(strlen($chapURLs['text'][$x])>0)
					{
						// get the chapter content, be aware that the $chapURLs['link'][$x] could have the a full URL.
						try{
							$content = $this->getContent($chapURL['link'][0]); 
						//*
						} catch(Exception $e) {
							// TODO think in a way to handle this exception BETTER.
							$content = $e->getMessage();
						}
						//*/
					
						// save the link (chapter)
						$subLinks[] = array(
							'text'=>$chapURLs['text'][$x], 
							'link'=> $chapURLs['link'][$x], 
							'content' => $content,
						);
					}
				} 
			}
		}
		
		return $subLinks;
	}
	
	
	
} // end class

?>
