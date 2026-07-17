<?php

/**
 *
 * @var object $matching_db database
 */

use Medoo\Medoo;

require_once $mod_root.'ajax/_include.php';
include_once SE_ROOT.'app/functions/functions.php';

global $hidden_csrf_token;

$this_uri = '/admin/addons/plugin/media-pro/start/';

if(!isset($_SESSION['media_filter'])) {
    $_SESSION['media_filter'] = '';
}

$media_filter = $_SESSION['media_filter'];

if(!isset($_SESSION['order_column'])) {
    $_SESSION['order_column'] = 'media_upload_time';
}

if(!isset($_SESSION['sort_direction'])) {
    $_SESSION['sort_direction'] = 'DESC';
}


$items_per_page = 10;
$items_start = $_SESSION['mp_items_start'] ?? 0;
$items_per_page = 25;


$all_images = $db_content->select("se_media","*",[
    "AND" => [
        "media_type[~]" => "image",
        "media_file[~]" => "$media_filter"
    ]
]);
$all_images = se_unique_multi_array($all_images,'media_file');
$cnt_all_images = count($all_images);

$order_column = $_SESSION['order_column'];

$get_images = $db_content->select("se_media","*",[
    "AND" => [
        "media_type[~]" => "image",
        "media_file[~]" => "$media_filter"
    ],
    "ORDER" => ["$order_column" => $_SESSION['sort_direction']],
    "LIMIT" => [$items_start,$items_per_page]
]);

// read entries from log
$log = $mediapro_db->select("log","src");

$get_images = se_unique_multi_array($get_images,'media_file');
$cnt_get_images = count($get_images);

$nbr_pages = ceil($cnt_all_images / $items_per_page);

$this_page = $items_start/$items_per_page;
$prev_start = $this_page-1;
if($prev_start < 0) {
    $prev_start = 0;
}
$next_start = $this_page+1;
if($next_start > $nbr_pages) {
    $next_start = $nbr_pages;
}

$pagination = '<div class="py-3 text-end">';
$pagination .= '<form hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-target="#listing">';

$pagination .= '<button class="btn btn-sm btn-default" name="set_page" value="0"><i class="bi bi-chevron-bar-left"></i></button>';
$pagination .= '<button class="btn btn-sm btn-default" name="set_page" value="'.$prev_start.'"><i class="bi bi-chevron-left"></i></button>';

for($i=0;$i<$nbr_pages;$i++){
    $btn_class = '';

    $stop_left = $this_page-5;
    $stop_right = $this_page+5;

    if(($i < $stop_left) && ($i < ($this_page-3))) {
        continue;
    }
    if(($i > $stop_right) && ($i > 4)) {
        continue;
    }

    if($this_page == $i){
        $btn_class = 'active';
    }
    $pagination .= '<button class="btn btn-sm btn-default '.$btn_class.'" name="set_page" value="'.$i.'">'.($i+1).'</button>';
}
$pagination .= '<button class="btn btn-sm btn-default" name="set_page" value="'.$next_start.'"><i class="bi bi-chevron-right"></i></button>';
$pagination .= '<button class="btn btn-sm btn-default" name="set_page" value="'.($nbr_pages-1).'"><i class="bi bi-chevron-bar-right"></i></button>';

$pagination .= '<input type="hidden" name="items_per_page" value="'.$items_per_page.'">';
$pagination .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
$pagination .= '</form>';
$pagination .= '</div>';

$sidebar = '<div class="card p-3">';

$sidebar .= '<form hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-target="#listing" class="mb-3">';
$sidebar .= '<input type="text" class="form-control" name="media_filter" value="'.$_SESSION['media_filter'].'">';
$sidebar .= '</form>';

$sidebar .= '<div class="row">';
$sidebar .= '<div class="col-md-8">';
$sidebar .= '<select name="sort_by" class="form-control" hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-target="#listing">';
$sidebar .= '<option value="1" '.($_SESSION['order_column'] == 'media_id' ? 'selected' :'').'>Sort by Upload</option>';
$sidebar .= '<option value="2" '.($_SESSION['order_column'] == 'media_file' ? 'selected' :'').'>Sort by Name</option>';
$sidebar .= '<option value="3" '.($_SESSION['order_column'] == 'media_filesize' ? 'selected' :'').'>Sort by Filesize</option>';
$sidebar .= '</select>';
$sidebar .= '</div>';
$sidebar .= '<div class="col-md-4">';
$sidebar .= '<div class="btn-group">';
$sidebar .= '<button type="submit" name="sort_direction" value="asc" hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-target="#listing" class="btn btn-default">'.$icon['arrow_up'].'</button>';
$sidebar .= '<button type="submit" name="sort_direction" value="desc" hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-target="#listing" class="btn btn-default">'.$icon['arrow_down'].'</button>';
$sidebar .= '</div>';
$sidebar .= '</div>';
$sidebar .= '</div>';

$sidebar .= '<input type="hidden" name="csrf_token" value="'.$_SESSION['token'].'">';
$sidebar .= '</div>';

try {
    \Tinify\setKey("$tinypng_api_key");
    \Tinify\validate();

    $compressionsThisMonth = \Tinify\compressionCount();
    echo 'This month we have compressed <code>'.$compressionsThisMonth.'</code> from <code>500</code> images</p>';

    echo '<div class="row">';
    echo '<div class="col-md-9">';

    echo $pagination;

    echo '<table class="table table-sm">';
    for($i=0;$i<$cnt_get_images;$i++) {

        $upload_time = (int) $get_images[$i]['media_upload_time'];
        $lastedit_time = (int) $get_images[$i]['media_lastedit'];

        $thumb = $get_images[$i]['media_thumb'];
        $thumb = str_replace('../','/',$thumb);
        $media_src = $get_images[$i]['media_file'];
        $media_src = str_replace('../','/',$media_src);
        $media_id = $get_images[$i]['media_id'];

        $compress_btn = '<button name="compress_image_id" value="'.$media_id.'" hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-include="[name=csrf_token]" hx-target="#response_'.$media_id.'" class="btn btn-default">Compress</button>';

        $info_btn = '<button name="edit-id" value="'.$media_id.'" hx-post="/admin-xhr/addons/plugin/media-pro/write/" hx-include="[name=csrf_token]" data-bs-toggle="modal" data-bs-target="#modal_'.$media_id.'" hx-target="#modal_'.$media_id.'" class="btn btn-default">'.$icon['edit'].' edit</button>';
        $info_btn .= '<div id="modal_'.$media_id.'" class="modal modal-blur fade" style="display: none">';
        $info_btn .= '<div class="modal-dialog modal-lg modal-dialog-centered" role="document"><div class="modal-content"></div></div>';
        $info_btn .= '</div>';

        $status = '<span class="text-warning">never compressed</span>';
        if(in_array("$media_src",$log)) {
            $status = '<span class="text-success">compressed</span>';
        }

        if($get_images[$i]['media_filesize'] > 10000) {
            $status_icon = '<span class="text-danger">'.$icon['exclamation_triangle'].'</span>';
        } else {
            $status_icon = '<span class="text-success">'.$icon['check'].'</span>';
        }

        echo '<tr>';
        echo '<td><img src="'.$media_src.'" width="50" alt="'.$get_images[$i]['media_thumb'].'"></td>';
        echo '<td>'.$media_src.'</td>';
        echo '<td>'.date("Y-m-d H:i",$upload_time).'</td>';
        echo '<td>'.$status_icon.readable_filesize($get_images[$i]['media_filesize']).'<br>'.$status.'</td>';
        echo '<td>'.$compress_btn.' '.$info_btn.'</td>';
        echo '</tr>';

        echo '<tr>';
        echo '<td colspan="5"><div id="response_'.$media_id.'"></div></td>';
        echo '</tr>';

    }
    echo '</table>';

    echo $pagination;

    echo '</div>';
    echo '<div class="col-md-3">';
    echo $sidebar;
    echo '</div>';
    echo '</div>';


} catch(\Tinify\Exception $e) {
    // Validation of API key failed.
    echo '<div class="alert alert-danger">';
    echo 'Validation of API key failed.';
    echo '</div>';
}