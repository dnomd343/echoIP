<?php

class countryDB extends SQLite3 {
    function __construct() {
        $this->open('country.db'); // 国家地区缩写及代号数据库
    }
}

function getCountry($code) { // 根据两位国家代码获取英文与中文全称
    $db = new countryDB;
    if ($code == null) {
        return null;
    }
    $dat = $db->query('SELECT * FROM main WHERE alpha_2="'.$code.'";')->fetchArray(SQLITE3_ASSOC);
    if ($dat) {
        $name_dat['en'] = $code." - ".$dat['name_en'];
        $name_dat['cn'] = $dat['name_cn'];
    } else {
        $name_dat['en'] = $code." - Unknow";
        $name_dat['cn'] = null;
    }
    return $name_dat;
}
