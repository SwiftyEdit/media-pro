<?php

/**
 * media-pro.mod Database-Scheme
 * install/update the table for preferences
 *
 */

$database = "media-pro";
$table_name = "settings";

$cols = array(
    "id"  => 'INTEGER NOT NULL PRIMARY KEY',
    "key" => 'VARCHAR',
    "value" => 'VARCHAR'
);