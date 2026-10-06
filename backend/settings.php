<?php


require SE_ROOT.'plugins/media-pro/vendor/autoload.php';
require SE_ROOT.'plugins/media-pro/backend/include.php';

if(isset($_POST['rebase_data'])) {
    $search = '../content/images/';
    $replace = '/images/';
    $mediapro_db->replace("log", ["src" => [ "$search" => "$replace" ]]);
}

if(isset($_POST['apikey'])) {
    $key = trim($_POST['apikey']);
    $api_file = fopen($mod_root.'data/apikey.php',"w");
    $content = "<?php\n";
    $content .= "\$tinypng_api_key=".var_export($key, true).";";
    fwrite($api_file, $content);
    fclose($api_file);
    include $mod_root.'data/apikey.php';
}


if(($tinypng_api_key ?? '') == '') {
    echo '<div class="alert alert-info">';
    echo 'We need an TinyPNG API key to use this Addon';
    echo '</div>';
}


echo '<div class="card p-3">';
echo '<form action="/admin/addons/plugin/media-pro/settings/" method="POST">';
echo '<div class="mb-1">';
echo '<label for="authkey">API Key</label>';
echo '<input type="password" id="apikey" class="form-control" name="apikey" value="'.htmlspecialchars($tinypng_api_key ?? '').'">';
echo '</div>';
echo $hidden_csrf_token;
echo '<button class="btn btn-default text-success">'.$lang['button_save'].'</button>';
echo '</form>';

echo '</div>';

echo '<form action="/admin/addons/plugin/media-pro/settings/" method="POST" class="mt-3 card">';
echo '<p class="p-3">If you are coming from SwiftyEdit v1.x ...</p>';
echo '<div class="card-footer"><button class="btn btn-sm btn-default w-auto" name="rebase_data">Rebase database</button></div>';
echo $hidden_csrf_token;
echo '</form>';