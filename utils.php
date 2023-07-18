<?php

function encode_email($e) {
    $output = "";
    for ($i = 0; $i < strlen($e); $i++) { $output .= '&#'.ord($e[$i]).';'; }
    return $output;
}

function end_module_session() {
	global $_NOTE;
	unset($GLOBALS['_NOTE']['UID']);
	unset($GLOBALS['_NOTE']['PAGE']);
	unset($_SESSION['uniqueID_'.$_NOTE["MODULE_ID"]]);
	session_destroy();
}

/**
 * process_get: Take the server GET parameters and return specified variables as an array
 * 
 * @param Array $vars Variables to look for
 * @return Array $returns Variables found
 */
function process_get(array $vars) 
{
	$return_vars = [];
	if ($_SERVER['REQUEST_METHOD'] === 'GET') {
		// Parse the URL into array of path, query string etc.
		$url = parse_url($_SERVER['REQUEST_URI']);
		// Parse the query string into an array called $gets
		parse_str($url['query'], $gets);
		// For each of the variables requested, look for a GET parameter
		foreach ($vars as $var) {
			if (isset($gets[$var])) {
				// Sanitise the GET parameter and add it to the array of return values
				$return_vars[$var] = htmlspecialchars(trim($gets[$var]));
			}
		}
	}
	return $return_vars;
}
?>