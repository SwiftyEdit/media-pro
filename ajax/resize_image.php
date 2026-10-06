<?php

//error_reporting(E_ALL);
use Medoo\Medoo;

$mod_root = SE_ROOT.'/plugins/media-pro/';
require_once $mod_root.'backend/include.php';
require_once $mod_root.'ajax/_include.php';
include_once SE_ROOT.'app/functions/functions.php';

$id = (int) $_POST['id'];
$resize_to = (int) $_POST['resize'];

/* read media data from $db_content */
$get_data = $db_content->get("se_media","*",[
    "media_id" => "$id"
]);

$filename = str_replace("../images/","",$get_data["media_file"]);
$path_info = pathinfo($filename);

$source_file = '../public/assets/images/'.$path_info['dirname'].'/'.$path_info['filename'].'.'.$path_info['extension'];
$destination = '../public/assets/images/'.$path_info['dirname'].'/'.$path_info['filename'].'.'.$path_info['extension'];
$init_filesize = (int) $get_data["media_filesize"];

try {
    mp_tinify_set_key((string) $tinypng_api_key);
    \Tinify\validate();

    $compressionsThisMonth = \Tinify\compressionCount();
    if($compressionsThisMonth < 500) {
        // compress now ...

        $source = \Tinify\fromFile("$source_file");

        $resized = $source->resize(array(
            "method" => "scale",
            "width" => $resize_to
        ));

        $resized->toFile("$destination");

        // update $db_content - se_media - $get_data["media_file"]
        $new_filesize = filesize($destination);
        $update_media = $db_content->update("se_media", [
            "media_filesize" => $new_filesize,
            "media_lastedit" => time()
        ], [
            "media_file" => $get_data["media_file"]
        ]);

        $insert_log = $mediapro_db->insert("log", [
            "time" => time(),
            "src" => '/images/'.$filename
        ]);

        $reduced = $init_filesize-$new_filesize;
        echo '<div class="alert alert-primary">';
        echo 'We reduced filsize by '.readable_filesize($reduced).'<br>';
        echo 'New width: <code>'.$resize_to.'</code>';
        echo '</div>';


    }

} catch(\Tinify\Exception $e) {
    // Validation of API key failed.
    echo '<div class="alert alert-danger">';
    print("Something went wrong: " . $e->getMessage());
    echo '</div>';
}