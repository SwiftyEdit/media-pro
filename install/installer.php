<?php

/**
 * @var string $mod_db from include.php
 */

/* INSTALL */

if(!is_file("$mod_db")) {

    echo '<p class="alert alert-info">We try to generate SQLite File: '.$mod_db.'</p>';

    $sql_uploads_table = generate_sql_query(SE_ROOT."/plugins/media-pro/install/tpl-log.php");
    $sql_settings_table = generate_sql_query(SE_ROOT."/plugins/media-pro/install/tpl-settings.php");

    $dbh = new PDO("sqlite:$mod_db");

    $dbh->query($sql_uploads_table);
    $dbh->query($sql_settings_table);

    $mod_version = $mod['version'];

    $sql_insert = "INSERT INTO settings (id,key,value
				) VALUES (
			NULL,'version','$mod_version')";

    $dbh->query($sql_insert);
    $dbh = null;

    @chmod("$mod_db", 0775);

    echo '<p class="alert alert-info">Generated SQLite File: '.$mod_db.'</p>';
    echo '<div class="alert alert-info">';
    echo 'Tables';
    echo '<pre>'.trim($sql_uploads_table).'</pre>';
    echo '<pre>'.trim($sql_settings_table).'</pre>';
    echo '</div>';

}


/* CHECK DATABASE */

if(!is_file("$mod_db")) {
    echo '<div class="alert alert-danger">File ('.$mod_db.') not found</div>';
}


/* UPDATE MAYBE */

$mediapro_prefs = mp_getSettings();

if(is_array($mediapro_prefs) && version_compare($mediapro_prefs['version']['value'], $mod['version'], '<')) {

    echo '<p class="alert alert-info">';
    echo 'an update is running ...';
    echo 'ratings version: '.$mediapro_prefs['version']['value'];
    echo '</p>';

    /* build an array from all tpl-xxx.php files in folder supplies */
    $all_tables = glob(SE_CONTENT."/modules/media-pro.mod/install/tpl-*.php");

    for($i=0;$i<count($all_tables);$i++) {

        unset($table_name);
        include("$all_tables[$i]"); // returns $cols and $table_name

        $is_table = table_exists("$mod_db","$table_name");

        if($is_table < 1) {
            add_table("$mod_db","$table_name",$cols);
            $table_updates[] = "<div class='alert alert-info'>New Table: <b>$table_name</b> in Database <b>$database</b></div>";
        }

        $existing_cols = get_collumns("$mod_db","$table_name");

        foreach ($cols as $k => $v) {
            if(!array_key_exists("$k", $existing_cols)) {
                //update_table -> column, type, table, database
                update_table("$k","$cols[$k]","$table_name","$mod_db");
                $col_updates[] = "<div class='alert alert-info'>New Column: <b>$k</b> in table <b>$table_name</b></div>";
            }
        }

        /* updates are done, check all columns again */

        $existing_cols = get_collumns("$mod_db","$table_name");

        foreach ($cols as $b => $x) {
            if(!array_key_exists("$b", $existing_cols)) {
                $fails[] = "<div class='alert alert-error'>Missing Column: <b>$b</b> - table: <b>$table_name</b></div>";
            } else {
                $wins[] = "<div class='alert alert-success'>Column <b>$b</b> in table <b>$table_name</b> is ready</div>";
            }
        }


    } // EO $i


    /* increase version number in $mod_db */

    $dbh = new PDO("sqlite:$mod_db");
    $sql = "UPDATE settings
					SET value = '$mod[version]'
					WHERE key = 'version' ";
    $cnt_changes = $dbh->exec($sql);
    $dbh = null;


}


/* echo fails and wins */

if(is_array($fails)) {
    foreach ($fails as $value) {
        echo"$value";
    }

}


if(is_array($wins)) {

    if(is_array($table_updates)) {
        foreach ($table_updates as $value) {
            echo"$value";
        }
    }

    if(is_array($col_updates)) {
        foreach ($col_updates as $value) {
            echo"$value";
        }
    }

}






/* returns all cols and types of a existung database/table */
function get_collumns($db,$table_name) {

    $dbh = new PDO("sqlite:$db");

    $result = $dbh->query("PRAGMA table_info(" . $table_name . ")");
    $result->setFetchMode(PDO::FETCH_ASSOC);
    $meta = array();
    foreach ($result as $row) {
        $meta[$row['name']] = $row['type'];
    }

    return $meta;

}



/*  check if table exists - returns the number of existing tables */
function table_exists($db,$table_name) {

    $dbh = new PDO("sqlite:$db");

    $result = $dbh->query("SELECT COUNT(*) FROM sqlite_master WHERE type='table' AND name='$table_name'")->fetch();
    $cnt_tables = $result[0];

    return $cnt_tables;
}


/**
 * generate an sql query from templates (php files)
 * @return string
 * @var string $database
 * @var string $table_name
 * @var string $table_type null or 'virtual'
 * @var array $cols from include file $file
 */

function generate_sql_query($file): string {

    include "$file";
    $string = '';

    foreach ($cols as $k => $v) {
        $string .= "$k $v,\r";
    }

    $string = substr(trim("$string"), 0,-1); // cut last comma and returns

    if($table_type == 'virtual') {
        $sql_string = "CREATE VIRTUAL TABLE $table_name USING fts3($string,tokenize=porter)";
    } else {
        $sql_string = "
				CREATE TABLE $table_name (
				$string
				)
			";
    }

    /* return the sql string */
    return $sql_string;
}




function update_table($col_name,$type,$table_name,$db) {

    $dbh = new PDO("sqlite:$db");

    $sql = "ALTER TABLE $table_name ADD $col_name $type";
    $dbh->exec($sql);

    $dbh = null;

}


function add_table($db,$table_name,$cols) {

    foreach ($cols as $k => $v) {
        $cols_string .= "$k $cols[$k],\r";
    }

    $cols_string = substr(trim("$cols_string"), 0,-1); // cut last commata and returns

    $dbh = new PDO("sqlite:$db");

    $sql = "CREATE TABLE $table_name (
							$cols_string
							)";
    $dbh->exec($sql);

    $dbh = null;

}
