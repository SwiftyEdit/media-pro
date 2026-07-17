<?php

if(isset($_POST['set_page'])) {
    $items_per_page = (int) $_POST['items_per_page'];
    $items_start = $_POST['set_page']*$items_per_page;
    $_SESSION['mp_items_start'] = $items_start;
    //echo '<div class="d-flex align-items-center htmx-indicator"><div class="spinner-border spinner-border-sm me-2" role="status"></div><span class="sr-only">Reloading...</span></div>';
    header('HX-Trigger: listmedia');
}

if(isset($_POST['compress_image_id'])) {
    include SE_ROOT.'plugins/media-pro/ajax/compress_image.php';
}

if(isset($_POST['edit-id'])) {
    include SE_ROOT.'plugins/media-pro/ajax/edit_image.php';
}

if(isset($_POST['resize'])) {
    require_once SE_ROOT.'plugins/media-pro/ajax/resize_image.php';
}

if(isset($_POST['media_filter'])) {
    $_SESSION['media_filter'] = sanitizeUserInputs($_POST['media_filter']);
    //echo '<div class="d-flex align-items-center htmx-indicator"><div class="spinner-border spinner-border-sm me-2" role="status"></div><span class="sr-only">Reloading...</span></div>';
    header('HX-Trigger: listmedia');
}

if(isset($_POST['sort_by'])) {
    $sort_by = (int)  $_POST['sort_by'];
    if($sort_by == 1) {
        $_SESSION['order_column'] = 'media_id';
    } else if($sort_by == 2) {
        $_SESSION['order_column'] = 'media_file';
    } else if($sort_by == 3) {
        $_SESSION['order_column'] = 'media_filesize';
    }
    //echo '<div class="d-flex align-items-center htmx-indicator"><div class="spinner-border spinner-border-sm me-2" role="status"></div><span class="sr-only">Reloading...</span></div>';
    header('HX-Trigger: listmedia');
}

if(isset($_POST['sort_direction'])) {
    if($_POST['sort_direction'] == 'asc') {
        $_SESSION['sort_direction'] = 'ASC';
    } else if($_POST['sort_direction'] == 'desc') {
        $_SESSION['sort_direction'] = 'DESC';
    }
    //echo '<div class="d-flex align-items-center htmx-indicator"><div class="spinner-border spinner-border-sm me-2" role="status"></div><span class="sr-only">Reloading...</span></div>';
    header('HX-Trigger: listmedia');
}