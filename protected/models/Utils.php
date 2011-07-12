<?php

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
function d($line,$var, $varname="variable")
{
	// regular use
	// d(__LINE__, $variable, '$variable')
	echo "\n#($line) ". $varname . ' = ';
	var_export($var);
	echo "\n";
} 


?>
