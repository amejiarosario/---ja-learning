<?php

/**
 * Regular expression evaluator
 * @param $regex regular expression
 * @param $text text to apply the regex
 * @return multi array of results from the evaluation of the regular expression (RegEx)
 *
 */
function regex($regex, $text='')
{
	if(!isset($regex) || strlen($regex)<3)
		throw new Exception('No regex string to evaluate.');
	// espape values --> replace '\' for '\\' OR '/' for '.'; ''' for '\'';
	preg_match_all($regex, $text, $result);
	return $result;
}

/**
 * Here is a simple function to find the position of the next occurrence of needle in haystack, but searching backwards  (lastIndexOf type function)
 */
function rstrpos ($haystack, $needle, $offset)
{
    $size = strlen ($haystack);
    $pos = strpos (strrev($haystack), strrev($needle), $size - $offset);
   
    if ($pos === false)
        return false;
   
    return $size - $pos - strlen($needle);
}

/*
 * Debug print
 */
function d($line,$file,$var, $varname="variable")
{
	// example of use:
	// d(__LINE__,__FILE__, $variable, '$variable')
	
	echo "\n#($file:$line) ". $varname . ' = ';
	var_export($var);
	echo "\n";
} 


?>
