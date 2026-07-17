<?php

require SE_ROOT.'/plugins/media-pro/backend/include.php';
require SE_ROOT.'/plugins/media-pro/install/installer.php';

echo '<div class="card p-3">';
echo '<div id="listing" hx-include="[name=csrf_token]" hx-post="/admin-xhr/addons/plugin/media-pro/read/?list=media" hx-trigger="load, listmedia from:body">';
echo '<div class="d-flex align-items-center htmx-indicator"><div class="spinner-border spinner-border-sm me-2" role="status"></div><span class="sr-only">Loading...</span></div>';
echo '</div>';
echo '</div>';
