<?php

/**
 * media-pro.mod Database-Scheme
 * install/update the table for entries
 */

$database = "media-pro";
$table_name = "log";

$cols = array(
    "id"  => 'INTEGER NOT NULL PRIMARY KEY',
    "time" => 'INTEGER',
    "src"  => 'VARCHAR'
);