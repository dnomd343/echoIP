<?php

include("getCountry.php");
include("qqwry.php");

function getIPInfo($ip) {
    $qqwry = new QQWry();
    $detail = $qqwry->getDetail($ip);
    $specialInfo = getSpecialInfo($ip);
    if (is_string($specialInfo)) {
        $info['ip'] = $ip;
        $info['as'] = null;
        $info['city'] = null;
        $info['region'] = null;
        $info['country'] = null;
        $info['timezone'] = null;
        $info['loc'] = null;
        $info['isp'] = $specialInfo;
    } else {
        $rawIspInfo = getInfo($ip);
        $info['ip'] = $ip;
        $info['as'] = getAS($rawIspInfo);
        $info['city'] = $rawIspInfo['city'];
        $info['region'] = $rawIspInfo['region'];
        $info['country'] = getCountry($rawIspInfo['country'])['en'];
        $info['country'] .= "（".getCountry($rawIspInfo['country'])['cn']."）";
        $info['timezone'] = $rawIspInfo['timezone'];
        $info['loc'] = $rawIspInfo['loc'];
        $info['isp'] = getIsp($rawIspInfo);
    }
    $info['scope'] = tryCIDR($detail['beginIP'], $detail['endIP']);
    $info['detail'] = $detail['dataA'] . $detail['dataB'];

    if ($_GET['cli'] == "true") { // 使用命令行模式
        $cli = "IP: ".$info['ip'] . PHP_EOL;
        $cli .= "AS: ".$info['as'] . PHP_EOL;
        $cli .= "City: ".$info['city'] . PHP_EOL;
        $cli .= "Region: ".$info['region'] . PHP_EOL;
        $cli .= "Country: ".$info['country'] . PHP_EOL;
        $cli .= "Timezone: ".$info['timezone'] . PHP_EOL;
        $cli .= "Location: ".$info['loc'] . PHP_EOL;
        $cli .= "ISP: ".$info['isp'] . PHP_EOL;
        $cli .= "Scope: ".$info['scope'] . PHP_EOL;
        $cli .= "Detail: ".$info['detail'] . PHP_EOL;
        return $cli;
    }

    header('Content-Type: application/json; charset=utf-8'); // 以JSON格式发送
    return json_encode($info);
}

function getSpecialInfo($ip) { // 识别特殊IP地址
    if ('::1' === $ip) {
        return 'localhost IPv6 access';
    }
    if (stripos($ip, 'fe80:') === 0) {
        return 'link-local IPv6 access';
    }
    if (strpos($ip, '127.') === 0) {
        return 'localhost IPv4 access';
    }
    if (strpos($ip, '10.') === 0) {
        return 'private IPv4 access';
    }
    if (preg_match('/^172\.(1[6-9]|2\d|3[01])\./', $ip) === 1) {
        return 'private IPv4 access';
    }
    if (strpos($ip, '192.168.') === 0) {
        return 'private IPv4 access';
    }
    if (strpos($ip, '169.254.') === 0) {
        return 'link-local IPv4 access';
    }
    return null;
}

function getInfo($ip) { // 获取IP详细信息
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

function getIsp($rawIspInfo) { // 提取ISP信息
    if (
        !is_array($rawIspInfo)
        || !array_key_exists('org', $rawIspInfo)
        || !is_string($rawIspInfo['org'])
        || empty($rawIspInfo['org'])
    ) {
        return 'Unknown ISP';
    }
    return preg_replace('/AS\\d+\\s/', '', $rawIspInfo['org']);
}

function getAS($rawIspInfo) { // 提取AS信息
    if (
        !is_array($rawIspInfo)
        || !array_key_exists('org', $rawIspInfo)
        || !is_string($rawIspInfo['org'])
        || empty($rawIspInfo['org'])
    ) {
        return 'Unknown AS';
    }
    if (preg_match('/AS\\d+\\s/', $rawIspInfo['org'], $as) !== 1) {
        return 'Unknown AS';
    }
    return trim($as['0']);
}

function tryCIDR($beginIP, $endIP) {
    $tmp = ip2long($endIP) - ip2long($beginIP) + 1;
    if (pow(2, intval(log($tmp, 2))) == $tmp) {
        return $beginIP . '/' . (32 - log($tmp, 2));
    } else {
        return $beginIP . ' - ' . $endIP;
    }
}

?>
