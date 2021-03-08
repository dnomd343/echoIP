<?php
$db_path = 'country.db';
$db = new SQLiteDB;

class SQLiteDB extends SQLite3 {
    function __construct() {
        global $db_path;
        $this->open($db_path);
    }
}

function get_country($code) {
    global $db;
    if ($code == null) {
        return null;
    }
    $dat = $db->query('SELECT * FROM main WHERE alpha_2="'.$code.'";')->fetchArray(SQLITE3_ASSOC);
    if ($dat) {
        $name_dat['en'] = $code." - ".$dat['name_en'];
        $name_dat['cn'] = $dat['name_cn'];//."（".$dat['location']."）";
    } else {
        $name_dat['en'] = $code." - Unknow";
        $name_dat['cn'] = null;
    }
    return $name_dat;
}
