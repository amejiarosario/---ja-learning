<?php
/*
@mustsee http://gskinner.com/RegExr/
@see http://www.spaweditor.com/scripts/regex/index.php
@seealso http://www.regexpal.com/
*/

function d($line,$var, $varname="variable")
{
  //echo "\n#($line) ". $varname . ' = ';
  //var_export($var);
  //echo "\n";
}

function getUrlElements($link)
{
	// TODO: handle exception URL without domain. E.g. href="/doc/guide/"
  $domain = array();
  preg_match('/([a-zA-Z0-9]([a-zA-Z0-9\-]{0,61}[a-zA-Z0-9])?\.)+[a-zA-Z]{2,6}/',$link,$match);
  $domain['domain'] = $match[0];
  $domain['path'] = substr($link,strpos($link,$match[0])+strlen($match[0]));
  //$domain[] = $link;
  return $domain;
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

/**
 * @return an array with title and link of the chapters in the Tutorial
 */
function getTutChapters($websiteLink)
{
	$chapters = array();
	
	// 1. load website in memory
	//$web = file_get_contents($websiteLink);
  $web = '<a href="/demos/">Demos</a> <a href="www.example.com/doc/guide/">Guide</a> <script type="text/javascript" src="http://www.adrianmejiarosario.com/modules/toolbar/toolbar.js?lnrr1c"></script> '; //TODO remove after debug
	$tutUrl = getUrlElements($websiteLink);
	
	// 2. identify all the links in the website. e.g. <a href="/doc/guide/">Guide</a>
	preg_match_all('/<a href="[^"]+"[^>]*>[^<]+<\/a>/i',$web, $aTags);
	
  d(__LINE__,$aTags,'$links');
  
	// 3. Store only the ones that are in the same path or deeper.
	foreach($aTags[0] as $aTag)
	{
		$linkUrl = getUrlElements($aTag);
    $aTagElem = getATagElements($aTag); // link, name
    
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

//------- Main ---------
function main()
{
  echo "Tester v.3\n\n";
  $tut = "http://www.yiiframework.com/doc/guide/";

  $chapters = getTutChapters($tut);
  print_r($chapters);
}

//main();

?>
