<?
include_once('../config/config.php');
include_once(CORE_ROOT.'/classRoutes.php');

if(!file_exists(UPLOAD_FILES_ROOT)){
    mkdir(UPLOAD_FILES_ROOT);
}

$routes = new myRoutes();
echo $routes->actionRoute();
?>