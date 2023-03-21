<?php

// Define global state array
$_NOTE['RAW_URI'] = $_SERVER['REQUEST_URI'];
$_NOTE['BASE_URI'] = substr($_SERVER['SCRIPT_NAME'],0,-strlen("index.php"));
$_NOTE['REQUEST_URI'] = substr($_SERVER['REQUEST_URI'],strlen($_NOTE['BASE_URI']));
$_NOTE['MODULE_ID'] = NULL;
$_NOTE['DISPLAY_ID'] = NULL;
$_NOTE['OTHER_ID'] = NULL;
/**
 * The URL could be in two formats:
 * /<MODULE>/<OTHER>
 * /<data|preview>/<MODULE>/<OTHER>
 * The code must determine which format is in use and then parse the sections of the URL.
 */
$url = explode("/",trim($_NOTE['REQUEST_URI'],"/"),3);
if (!empty($url[0])) {
	if (in_array($url[0], ['data', 'preview'])) {
		$_NOTE['DISPLAY_ID'] = $url[0];
		$_mod_dir = __DIR__.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$url[1];
		$_NOTE['MODULE_ID'] = $url[1];
		$_NOTE['OTHER_ID'] = isset($url[2]) ? $url[2] : NULL;
	} else {
		$_NOTE['DISPLAY_ID'] = 'exp';
		$_mod_dir = __DIR__.DIRECTORY_SEPARATOR.'modules'.DIRECTORY_SEPARATOR.$url[0];
		$_NOTE['MODULE_ID'] = $url[0];
		$_NOTE['OTHER_ID'] = isset($url[1]) ? $url[1] : NULL;
	}

	if (file_exists($_mod_dir) && is_dir($_mod_dir)) {
		$_NOTE['MODULE_DIR'] = $_mod_dir;
		$_NOTE['MODULE_PATH'] = $_NOTE['BASE_URI'].'modules/'.$_NOTE['MODULE_ID'];
		$_NOTE['FS_MODULE_PATH'] = 'modules'.DIRECTORY_SEPARATOR.$_NOTE['MODULE_ID'].DIRECTORY_SEPARATOR;
	}
}

$_NOTE['PART_ID'] = $_NOTE['OTHER_ID']; // PART_ID is deprecated but set to OTHER_ID for now.
$_NOTE['PAGE'] = 0; // Stores position/progress reference for current module
$_NOTE['UID'] = NULL; // Stores unique visitor ID for current module

?>
