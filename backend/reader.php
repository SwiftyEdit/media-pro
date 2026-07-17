<?php

require_once SE_ROOT.'/plugins/media-pro/backend/include.php';

if(isset($_REQUEST['list']) && $_REQUEST['list'] == 'media') {
    require_once $mod_root.'ajax/listmedia.php';
    exit;
}