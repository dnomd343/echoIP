<?php

// 数据来源：country.db
// 请求方式：getCountry($code)
// 返回格式：
// {
//     "code": 国家的2位编码
//     "en": 国家英文名称
//     "cn": 国家中文名称
// }

class countryDB extends SQLite3 {
    function __construct() {
        $this->open('country.db'); // 国家地区缩写及代号数据库
    }
}

function getCountry($code) { // 根据两位国家代码获取英文与中文全称
    if ($code == null) {
        return null;
    }
    $db = new countryDB;
    $raw = $db->query('SELECT * FROM main WHERE alpha_2=\'' . $code . '\';')->fetchArray(SQLITE3_ASSOC);
    $data['code'] = $code;
    if ($raw) {
        $data['en'] = $raw['name_en'];
        $data['cn'] = $raw['name_cn'];
    } else {
        $data['en'] = null;
        $data['cn'] = null;
    }
    return $data;
}

?>