<?php

// data from: https://www.iana.org/assignments/iana-ipv4-special-registry/iana-ipv4-special-registry.xhtml

$specialIPv4 = array(
    [
        'scope' => '0.0.0.0/8', // 0.0.0.0 - 0.255.255.255
        'desc' => 'Self Identification'
    ],
    [
        'scope' => '10.0.0.0/8', // 10.0.0.0 - 10.255.255.255
        'desc' => 'Private Use Networks'
    ],
    [
        'scope' => '100.64.0.0/10', // 100.64.0.0 - 100.127.255.255
        'desc' => 'Shared Address Space'
    ],
    [
        'scope' => '127.0.0.0/8', // 127.0.0.0 - 127.255.255.255
        'desc' => 'Loopback'
    ],
    [
        'scope' => '169.254.0.0/16', // 169.254.0.0 - 169.254.255.255
        'desc' => 'Link Local'
    ],
    [
        'scope' => '172.16.0.0/12', // 172.16.0.0 - 172.31.255.255
        'desc' => 'Private Use Networks'
    ],
    [
        'scope' => '192.0.0.0/29', // 192.0.0.0 - 192.0.0.7
        'desc' => 'IPv4 Service Continuity Prefix'
    ],
    [
        'scope' => '192.0.0.8/32', // 192.0.0.8
        'desc' => 'IPv4 dummy address'
    ],
    [
        'scope' => '192.0.0.9/32', // 192.0.0.9
        'desc' => 'Port Control Protocol Anycast'
    ],
    [
        'scope' => '192.0.0.10/32', // 192.0.0.10
        'desc' => 'Traversal Using Relays around NAT Anycast'
    ],
    [
        'scope' => '192.0.0.170/32', // 192.0.0.170
        'desc' => 'NAT64/DNS64 Discovery'
    ],
    [
        'scope' => '192.0.0.171/32', // 192.0.0.171
        'desc' => 'NAT64/DNS64 Discovery'
    ],
    [
        'scope' => '192.0.0.0/24', // 192.0.0.0 - 192.0.0.255
        'desc' => 'IETF Protocol Assignments'
    ],
    [
        'scope' => '192.0.2.0/24', // 192.0.2.0 - 192.0.2.255
        'desc' => 'TEST-NET-1'
    ],
    // [
    //     'scope' => '192.31.196.0/24', // 192.31.196.0 - 192.31.196.255
    //     'desc' => 'AS112-v4'
    // ],
    [
        'scope' => '192.52.193.0/24', // 192.52.193.0 - 192.52.193.255
        'desc' => 'AMT'
    ],
    [
        'scope' => '192.88.99.0/24', // 192.88.99.0 - 192.88.99.255
        'desc' => 'Deprecated (6to4 Relay Anycast)'
    ],
    [
        'scope' => '192.168.0.0/16', // 192.168.0.0 - 192.168.255.255
        'desc' => 'Private Use Networks'
    ],
    // [
    //     'scope' => '192.175.48.0/24', // 192.175.48.0 - 192.175.48.255
    //     'desc' => 'Direct Delegation AS112 Service'
    // ],
    [
        'scope' => '198.18.0.0/15', // 198.18.0.0 - 198.19.255.255
        'desc' => 'Benchmarking'
    ],
    [
        'scope' => '198.51.100.0/24', // 198.51.100.0 - 198.51.100.255
        'desc' => 'TEST-NET-2'
    ],
    [
        'scope' => '203.0.113.0/24', // 203.0.113.0 - 203.0.113.255
        'desc' => 'TEST-NET-3'
    ],
    [
        'scope' => '224.0.0.0/4', // 224.0.0.0 - 239.255.255.255
        'desc' => 'IPv4 Class D for Multicasting'
    ],
    [
        'scope' => '255.255.255.255/32', // 255.255.255.255
        'desc' => 'Limited Broadcast'
    ],
    [
        'scope' => '240.0.0.0/4', // 240.0.0.0 - 255.255.255.255
        'desc' => 'IPv4 Class E Reserved'
    ]
);

function cidrToRange($cidr) { // CIDR转IP段
    $cidr = explode('/', $cidr);
    $range['start'] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
    $range['end'] = long2ip((ip2long($range['start'])) + pow(2, (32 - (int)$cidr[1])) - 1);
    return $range;
}

function checkSpecialIPv4($ip) { // 检查特殊IPv4地址
    global $specialIPv4;
    $ipv4 = ip2long($ip);
    foreach ($specialIPv4 as $special) {
        $range = cidrToRange($special['scope']);
        if ($ipv4 >= ip2long($range['start']) && $ipv4 <= ip2long($range['end'])) {
            $detail = (new QQWry)->getDetail($ip);
            return array(
                'scope' => $special['scope'],
                'descEn' => $special['desc'],
                'descCn' => $detail['dataA'] . $detail['dataB']
            );
        }
    }
    return null; // 非特殊地址
}

function checkSpecialIPv6($ip) { // 检查特殊IPv6地址
    // TODO: More IPv6 range
    if ('::1' === $ip) {
        $info['scope'] = '::1/128';
        $info['en'] = 'localhost IPv6 access';
        $info['cn'] = '本地IPv6地址';
    }
    if (stripos($ip, 'fe80:') === 0) {
        $info['scope'] = 'fe80::/16';
        $info['en'] = 'link-local IPv6 access';
        $info['cn'] = '链路本地IPv6地址';
    }
    return isset($info) ? $info : null;
}

function checkSpecial($ip) { // 检查特殊IP地址并返回说明
    if (filter_var($ip, \FILTER_VALIDATE_IP,\FILTER_FLAG_IPV4)) { // IPv4
        return checkSpecialIPv4($ip);
    } else {
        return checkSpecialIPv6($ip);
    }
}

?>