<?php

include("country.php");
include("qqwry.php");
include("ipinfo.php");
include("ipip.php");
include("city.php");

function getIPInfo($ip) {
    $specialInfo = checkSpecial($ip);
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
        $IPIP = new IPDB('ipipfree.ipdb');
        $addr = $IPIP->getDistrict($ip);
        $data = IPinfo::getInfo($ip);
        $country = getCountry($data['country']);
        $info['ip'] = $data['ip'];
        $info['as'] = $data['as'];
        $info['city'] = $data['city'];
        $info['region'] = $data['region'];
        $info['country'] = $data['country'] . ' - ' . $country['en'];
        $info['country'] .= "（" . $country['cn'] . "）";
        $info['timezone'] = $data['timezone'];
        $info['loc'] = $data['loc'];
        $info['isp'] = $data['isp'];
        if ($addr[0] == '中国') {
            $info['country'] = 'CN - China（中国）';
            $info['timezone'] = 'Asia/Shanghai';
            if ($addr[1] == '') {
                $addr[1] = '北京';
            }
            $cityLoc = getLoc($addr[1], $addr[2]);
            $info['region'] = $cityLoc['region'];
            $info['city'] = $cityLoc['city'];
            $info['loc'] = $cityLoc['lat'] . ',' . $cityLoc['lon'];
        }
    }
    if (filter_var($ip, \FILTER_VALIDATE_IP,\FILTER_FLAG_IPV4)) {
        $qqwry = new QQWry('qqwry.dat');
        $detail = $qqwry->getDetail($ip);
        $info['scope'] = tryCIDR($detail['beginIP'], $detail['endIP']);
        $info['detail'] = $detail['dataA'] . $detail['dataB'];
    } else {
        $info['scope'] = $info['ip'];
        $info['detail'] = $info['as'] . ' ' . $info['isp'];
    }

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

function checkSpecial($ip) { // 检查特殊IP地址并返回说明
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

function tryCIDR($beginIP, $endIP) { // 给定IP范围，尝试计算CIDR
    $tmp = ip2long($endIP) - ip2long($beginIP) + 1;
    if (pow(2, intval(log($tmp, 2))) == $tmp) { // 判断是否为2的整数次方
        return $beginIP . '/' . (32 - log($tmp, 2));
    } else {
        return $beginIP . ' - ' . $endIP;
    }
}

function main() {
    $ip = $_GET['ip'];
    if (!filter_var($ip, \FILTER_VALIDATE_IP)) {
        echo "Illegal IP format".PHP_EOL;
        exit;
    }
    echo getIPInfo($ip);
}

main();

?>
