<?php

// 数据来源：cityCN.db
// 请求方式：getLoc($region, $city)
// 返回格式：
// {
//     "region": 省/自治区/直辖市名称
//     "city": 市/自治州名称
//     "lat": 地区纬度
//     "lon": 地区经度
// }

class cityDB extends SQLite3 {
    function __construct() {
        $this->open('cityCN.db'); // 中国省市经纬度数据库
    }
}

function getLoc($region, $city) { // 根据省份/城市信息查询经纬度
    $db = new cityDB;
    $data['region'] = $region;
    $data['city'] = $city;
    $query_str='SELECT * FROM main WHERE level1="'.$region.'" AND level2="'.$city.'";';
    $raw = $db->query($query_str)->fetchArray(SQLITE3_ASSOC);
    if (!$raw) { // 查无数据
        $query_str='SELECT * FROM main WHERE level1="'.$region.'" AND level2="-";'; // 尝试仅查询省份数据
        $raw = $db->query($query_str)->fetchArray(SQLITE3_ASSOC);
        if (!$raw) { // 省份错误，返回北京经纬度
            $data['region'] = '北京';
            $data['city'] = '北京';
            $data['lat'] = '39.91';
            $data['lon'] = '116.73';
            return $data;
        }
        if ($city == '') {
            $query_str='SELECT * FROM main WHERE level1="'.$region.'" LIMIT 1,1;'; // 获取省会记录
            $raw = $db->query($query_str)->fetchArray(SQLITE3_ASSOC);
            $data['city'] = $raw['level2'];
        }
    }
    $data['lat'] = $raw['lat'];
    $data['lon'] = $raw['lon'];
    return $data;
}

?>