<?php

// 数据来源：https://ipinfo.io/$ip/json
// 请求方式：getInfo($ip)
// 返回格式：
// {
//     "ip": 请求IP
//     "as": AS信息
//     "city": 城市
//     "region": 行政区
//     "country": 国家
//     "timezone": 时区
//     "loc": 经纬度
//     "isp": ISP信息
// }

class IPinfo {
    public function getInfo($ip) {
        $rawInfo = self::getRawInfo($ip);
        $info['ip'] = $ip;
        $info['as'] = self::getAS($rawInfo);
        $info['city'] = $rawInfo['city'];
        $info['region'] = $rawInfo['region'];
        $info['country'] = $rawInfo['country'];
        $info['timezone'] = $rawInfo['timezone'];
        $info['loc'] = $rawInfo['loc'];
        $info['isp'] = self::getISP($rawInfo);
        return $info;
    }

    private function getRawInfo($ip) { // 获取IP信息
        $json = file_get_contents('https://ipinfo.io/' . $ip . '/json');
        if (!is_string($json)) {
            return null;
        }
        $data = json_decode($json, true);
        if (!is_array($data)) {
            return null;
        }
        return $data;
    }

    private function getISP($rawInfo) { // 提取ISP信息
        if (
            !is_array($rawInfo)
            || !array_key_exists('org', $rawInfo)
            || !is_string($rawInfo['org'])
            || empty($rawInfo['org'])
        ) {
            return null;
        }
        return preg_replace('/AS\\d+\\s/', '', $rawInfo['org']);
    }

    private function getAS($rawInfo) { // 提取AS信息
        if (
            !is_array($rawInfo)
            || !array_key_exists('org', $rawInfo)
            || !is_string($rawInfo['org'])
            || empty($rawInfo['org'])
        ) {
            return null;
        }
        if (preg_match('/AS\\d+\\s/', $rawInfo['org'], $as) !== 1) {
            return null;
        }
        return trim($as['0']);
    }
}

?>