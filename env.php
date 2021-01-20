<?php

// Define global state array
$_NOTE["RAW_URI"] = $_SERVER["REQUEST_URI"];
$_NOTE["BASE_URI"] = substr($_SERVER["SCRIPT_NAME"],0,-strlen("index.php"));
$_NOTE["REQUEST_URI"] = substr($_SERVER["REQUEST_URI"],strlen($_NOTE["BASE_URI"]));
$_NOTE["MODULE_ID"] = NULL;
$_NOTE["PART_ID"] = NULL;
$_NOTE["OTHER_ID"] = NULL;
$tmp = explode("/",trim($_NOTE["REQUEST_URI"],"/"),3);
if (isset($tmp[0]) && strlen($tmp[0]) > 0) {
	if ($tmp[0] === "data") {
		$_NOTE["MODULE_ID"] = $tmp[0];
	} else {
		$_mod_dir = __DIR__.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$tmp[0];
		if (file_exists($_mod_dir) && is_dir($_mod_dir)) {
			$_NOTE["MODULE_ID"] = $tmp[0];
			$_NOTE["MODULE_DIR"] = $_mod_dir;
			$_NOTE["MODULE_PATH"] = $_NOTE["BASE_URI"].'modules/'.$_NOTE["MODULE_ID"];
		}
	}
}
if (isset($tmp[1])) {
	if ($_NOTE["MODULE_ID"] === "data") {
		$_mod_dir = __DIR__.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$tmp[1];
		if (file_exists($_mod_dir) && is_dir($_mod_dir)) {
			$_NOTE["PART_ID"] = $tmp[1];
			$_NOTE["MODULE_DIR"] = $_mod_dir;
			$_NOTE["MODULE_PATH"] = $_NOTE["BASE_URI"].'modules/'.$_NOTE["MODULE_ID"];
		}
	} else {
		$_NOTE["PART_ID"] = $tmp[1];
	}
}
if (isset($tmp[2])) {
    $_NOTE["OTHER_ID"] = $tmp[2];
}

$_NOTE["PAGE"] = 0; // Stores position/progress reference for current module
$_NOTE["UID"] = NULL; // Stores unique visitor ID for current module

?>
