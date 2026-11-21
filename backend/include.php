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


/*
 * Core database connections ($db_content, $db_user, $db_posts).
 * Normally the core has opened them already - reuse those. Only when this file
 * is reached without them, open them the same way app/database.php does:
 * since core 2.4 (build 26-0238) SQLite is one file ($se_db_file), the old
 * content/user/posts.sqlite3 are only used while the core reports that the
 * merge failed ($se_db_state['mode'] === 'legacy') or on cores older than 2.4.
 */
if (isset($db_content) && $db_content instanceof Medoo) {
    $db_type = $db_type ?? 'sqlite';
    $db_user = (isset($db_user) && $db_user instanceof Medoo) ? $db_user : $db_content;
    $db_posts = (isset($db_posts) && $db_posts instanceof Medoo) ? $db_posts : $db_content;

} elseif (is_file(SE_ROOT.'/config_database.php')) {
    include SE_ROOT.'/config_database.php';
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

    $mp_legacy = empty($se_db_file) || (($se_db_state['mode'] ?? '') === 'legacy');

    if ($mp_legacy) {
        $db_content = new Medoo(['type' => 'sqlite', 'database' => "$se_db_content"]);
        $db_user = new Medoo(['type' => 'sqlite', 'database' => "$se_db_user"]);
        $db_posts = new Medoo(['type' => 'sqlite', 'database' => "$se_db_posts"]);
    } else {
        $db_content = new Medoo(['type' => 'sqlite', 'database' => "$se_db_file"]);
        $db_user = $db_content;
        $db_posts = $db_content;
    }
}