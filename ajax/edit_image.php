<?php

/**
 *
 * @var object $matching_db database
 * @var object $db_content database
 * @var string $tinypng_api_key from include file
 */

use Medoo\Medoo;
//error_reporting(E_ALL);

$mod_root = SE_ROOT.'/plugins/media-pro/';
require_once $mod_root.'backend/include.php';
require_once $mod_root.'ajax/_include.php';
include_once SE_ROOT.'app/functions/functions.php';

global $hidden_csrf_token;

$id = (int) $_POST['edit-id'];

echo '
<div class="modal-dialog modal-xl modal-dialog-centered">
  <div class="modal-content">
    <div class="modal-header">
      <h5 class="modal-title">Modal title</h5>
    </div>
    <div class="modal-body">';

    echo '<div id="modalResponse"></div>';

    /* read media data from $db_content */
    $get_data = $db_content->get("se_media","*",[
        "media_id" => "$id"
    ]);

    $image_src = str_replace("../images/","/images/",$get_data["media_file"]);
    $path_info = pathinfo($image_src);
    $source_file = '../public/assets'.$path_info['dirname'].'/'.$path_info['filename'].'.'.$path_info['extension'];

    if(file_exists("$source_file")) {
        $sizes = getimagesize("$source_file");
    } else {
        $sizes = "<p>NO FILE $source_file</p>";
    }

    echo '<div class="row">';
    echo '<div class="col-md-6">';
    echo '<img src="'.$image_src.'" class="img-fluid">';
    echo '</div>';
    echo '<div class="col-md-6">';
    echo '<table class="table table-sm table-borderless">';
    echo '<tr><td>src</td><td>'.$get_data['media_file'].'</td></tr>';
    echo '<tr><td>uploaded</td><td>'.date('Y-m-d H:i:s',$get_data['media_upload_time']).'</td></tr>';
    echo '<tr><td>last edit</td><td>'.date('Y-m-d H:i:s',$get_data['media_lastedit']).'</td></tr>';
    echo '<tr><td>size</td><td>'.readable_filesize($get_data['media_filesize']).'</td></tr>';
    echo '</table>';

    echo '<form hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-target="#modalResponse" hx-swap="innerHTML" hx-include="[name=csrf_token]">';
    echo '<button type="submit" class="btn btn-success" name="compress" value="'.$id.'">Compress</button>';
    echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['token'] . '">';
    echo '<input type="hidden" name="compress_image_id" value="' . $id . '">';
    echo '<input type="hidden" name="id" value="' . $id . '">';
    echo '</form>';

    if(is_array($sizes) && ($sizes[0] > 576)) {
        echo '<hr>';
        echo '<p>This picture seems to be quite large ('.$sizes[0].' px). Would you like to reduce width?</p>';
        echo '<form hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-target="#modalResponse" hx-swap="innerHTML" hx-include="[name=csrf_token]">';
        echo '<button type="submit" class="btn btn-success me-1" name="resize" value="992">w 992 px</button>';
        echo '<button type="submit" class="btn btn-success me-1" name="resize" value="768">w 768 px</button>';
        echo '<button type="submit" class="btn btn-success" name="resize" value="576">w 576 px</button>';
        echo '<input type="hidden" name="csrf_token" value="' . $_SESSION['token'] . '">';
        echo '<input type="hidden" name="resize_id" value="' . $id . '">';
        echo '<input type="hidden" name="id" value="' . $id . '">';
        echo '</form>';
    }


    echo '</div>';
    echo '</div>';




echo '
    </div>
    <div class="modal-footer">
      <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
    </div>
  </div>
</div>
';