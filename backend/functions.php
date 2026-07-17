<?php

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