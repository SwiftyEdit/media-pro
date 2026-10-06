<?php

/**
 * Set the TinyPNG API key.
 *
 * tinify/tinify forces CURLOPT_CAINFO to its bundled lib/data/cacert.pem, but
 * the SwiftyEdit installer rejects .pem files, so release zips ship without
 * it and every request fails with curl error #77. If the bundle is missing,
 * drop that option so curl falls back to the server's system CA store.
 *
 * @param string $key
 * @return void
 */
function mp_tinify_set_key(string $key): void {
    \Tinify\setKey($key);

    $ca_bundle = SE_ROOT.'plugins/media-pro/vendor/tinify/tinify/lib/data/cacert.pem';
    if (is_file($ca_bundle)) {
        return;
    }

    $client = new \Tinify\Client($key);
    \Closure::bind(function () {
        unset($this->options[CURLOPT_CAINFO]);
    }, $client, \Tinify\Client::class)();
    \Tinify\Tinify::setClient($client);
}

/**
 * @return mixed
 */
function mp_getSettings(): mixed {
    global $mediapro_db;

    if (!isset($mediapro_db)) {
        return '';
    } else {
        $prefs = $mediapro_db->query("SELECT key, value FROM settings")->fetchAll(PDO::FETCH_GROUP | PDO::FETCH_UNIQUE);
        return $prefs;
    }
}