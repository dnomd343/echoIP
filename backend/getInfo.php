<?php

include("getCountry.php");
include("qqwry.php");

function getIPInfo($ip) {
    $qqwry = new IpLocation();
    $detail = $qqwry->getDetail($ip);
    $specialIpInfo = getSpecialIpInfo($ip);
    if (is_string($specialIpInfo)) {
        $info['ip'] = $ip;
        $info['as'] = null;
        $info['city'] = null;
        $info['region'] = null;
        $info['country'] = null;
        $info['timezone'] = null;
        $info['loc'] = null;
        $info['isp'] = $specialIpInfo;
        $info['cidr'] = $detail['cidr'];
        $info['detail'] = $detail['addr'];
    } else {
        $rawIspInfo = getIspInfo($ip);
        $info['ip'] = $ip;
        $info['as'] = getAS($rawIspInfo);
        $info['city'] = $rawIspInfo['city'];
        $info['region'] = $rawIspInfo['region'];
        $info['country'] = get_country($rawIspInfo['country'])['en'];
        $info['country'] .= "（".get_country($rawIspInfo['country'])['cn']."）";
        $info['timezone'] = $rawIspInfo['timezone'];
        $info['loc'] = $rawIspInfo['loc'];
        $info['isp'] = getIsp($rawIspInfo);
        $info['cidr'] = $detail['cidr'];
        $info['detail'] = $detail['addr'];
    }

    if ($_GET['cli'] == "true") {
        $cli = "IP: ".$info['ip'].PHP_EOL;
        $cli .= "AS: ".$info['as'].PHP_EOL;
        $cli .= "City: ".$info['city'].PHP_EOL;
        $cli .= "Region: ".$info['region'].PHP_EOL;
        $cli .= "Country: ".$info['country'].PHP_EOL;
        $cli .= "Timezone: ".$info['timezone'].PHP_EOL;
        $cli .= "Location: ".$info['loc'].PHP_EOL;
        $cli .= "ISP: ".$info['isp'].PHP_EOL;
        $cli .= "CIDR: ".$info['cidr'].PHP_EOL;
        $cli .= "Detail: ".$info['detail'].PHP_EOL;
        return $cli;
    }

    header('Content-Type: application/json; charset=utf-8');
    return json_encode($info);
}

function getSpecialIpInfo($ip) {
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

function getIspInfo($ip) {
    $json = file_get_contents('https://ipinfo.io/'.$ip.'/json');
    if (!is_string($json)) {
        return null;
    }

    $data = json_decode($json, true);
    if (!is_array($data)) {
        return null;
    }
    return $data;
}

function getIsp($rawIspInfo) {
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

function getAS($rawIspInfo) {
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

?>
