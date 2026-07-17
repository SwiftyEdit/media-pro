<?php

require SE_ROOT.'/vendor/autoload.php';
use Medoo\Medoo;

//const SE_SECTION = "backend";

if($_SESSION['user_class'] != "administrator"){
    header("location:../../index.php");
    die("PERMISSION DENIED!");
}

// Defense in depth: these ajax/*.php files are plain, directly web-accessible
// PHP files, not exclusively reachable through the central /admin-xhr/ router.
// app/bootstrap.php already runs se_validate_token() globally for any
// non-empty POST, but that function only sets a redirect header without an
// exit, so script execution (and this endpoint's side effects) continues
// regardless. Re-check here so an invalid/missing token actually stops
// execution for this plugin's endpoints.
if (!empty($_POST) && ($_POST['csrf_token'] ?? '') !== ($_SESSION['token'] ?? null)) {
    http_response_code(403);
    die('CSRF token mismatch.');
}

include SE_ROOT."/acp/core/icons.php";


require SE_ROOT.'/config.php';
if(is_file(SE_CONTENT.'/config.php')) {
    include SE_CONTENT.'/config.php';
}

if (isset($_SESSION['lang'])) {
    $languagePack = basename($_SESSION['lang']);
}

include_once SE_ROOT."/languages/index.php";

global $db_content;