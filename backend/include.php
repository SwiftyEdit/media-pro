<?php

$mod_root = SE_ROOT.'/plugins/media-pro/';
$mod_db = $mod_root.'data/mediapro.sqlite3';

use Medoo\Medoo;
if(is_file($mod_db)) {
    $mediapro_db = new Medoo([
        'type' => 'sqlite',
        'database' => $mod_db
    ]);
} else {
    echo $mod_db.' not found';
}



include_once SE_ROOT.'/plugins/media-pro/vendor/autoload.php';
include_once SE_ROOT.'/plugins/media-pro/backend/functions.php';
require_once '../acp/core/functions.php';
require_once '../acp/core/icons.php';

if(is_file($mod_root.'data/apikey.php')) {
    include $mod_root.'data/apikey.php';
}

$mediapro_prefs = mp_getSettings();


if (is_file('../config_database.php')) {
    include '../config_database.php';
    $db_type = 'mysql';

    $database = new Medoo([
        'type' => 'mysql',
        'database' => "$database_name",
        'host' => "$database_host",
        'username' => "$database_user",
        'password' => "$database_psw",
        'charset' => 'utf8',
        'port' => $database_port,
        'prefix' => DB_PREFIX
    ]);

    $db_content = $database;
    $db_user = $database;
    $db_posts = $database;


} else {
    $db_type = 'sqlite';


    define("CONTENT_DB", "$se_db_content");
    define("USER_DB", "$se_db_user");
    define("POSTS_DB", "$se_db_posts");

    $db_content = new Medoo([
        'type' => 'sqlite',
        'database' => CONTENT_DB
    ]);

    $db_user = new Medoo([
        'type' => 'sqlite',
        'database' => USER_DB
    ]);

    $db_posts = new Medoo([
        'type' => 'sqlite',
        'database' => POSTS_DB
    ]);

}