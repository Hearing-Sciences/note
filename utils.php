<?php

function encode_email($e) {
    $output = "";
    for ($i = 0; $i < strlen($e); $i++) { $output .= '&#'.ord($e[$i]).';'; }
    return $output;
}

function end_module_session() {
	global $_SESSION;
	global $_NOTE;
	unset($GLOBALS['_NOTE']['UID']);
	unset($GLOBALS['_NOTE']['PAGE']);
	unset($_SESSION['uniqueID_'.$_NOTE["MODULE_ID"]]);
}

?>