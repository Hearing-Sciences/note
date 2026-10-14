<?php

$path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);

if (is_dir($_SERVER['DOCUMENT_ROOT'] . '/modules' . $path)) {
    error_log('Module ' . $_SERVER['DOCUMENT_ROOT'] . '/modules' . $path . ' found. Redirecting...');
    $_SERVER['SCRIPT_NAME'] = 'index.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/index.php';
    return true;
} elseif (substr($path, 0, 6) == '/data/') {
    error_log('Data page requested');
    $_SERVER['SCRIPT_NAME'] = 'data.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/data.php';
    return true;
} else {
    error_log('Not a module or module not found');
}

if (file_exists($_SERVER['DOCUMENT_ROOT'] . $path)) {
    error_log('File found. Displaying...');
    return false;
} else {
    error_log('File not found');
    $_SERVER['SCRIPT_NAME'] = 'index.php';
    include $_SERVER['DOCUMENT_ROOT'] . '/index.php';
}

