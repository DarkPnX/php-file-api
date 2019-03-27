<?
//const roots
define('SITE_ROOT', "..");
define('CORE_ROOT', SITE_ROOT . '/core');
define('UPLOAD_FILES_ROOT', SITE_ROOT .'/public/uploadFiles/');

//Data base config
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'file_api');
//Input name for upload file
define('INPUT_UPLOAD_FILE','clientfile');
define('MAX_SIZE_FILE','512000');
?>