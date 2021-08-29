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

// data from: https://www.iana.org/assignments/iana-ipv6-special-registry/iana-ipv6-special-registry.xhtml

$specialIPv6 = array(
    [
        'scope' => '::1/128',
        'range' => '::1 - ::1',
        'descEn' => 'Loopback Address',
        'descCn' => '环回地址'
    ],
    [
        'scope' => '::/128',
        'range' => ':: - ::',
        'descEn' => 'Unspecified Address',
        'descCn' => '未指定地址'
    ],
    [
        'scope' => '::ffff:0:0/96',
        'range' => '::ffff:0:0 - ::ffff:ffff:ffff',
        'descEn' => 'IPv4-mapped Address',
        'descCn' => 'IPv4映射地址'
    ],
    [
        'scope' => '64:ff9b::/96',
        'range' => '64:ff9b:: - 64:ff9b::ffff:ffff',
        'descEn' => 'IPv4-IPv6 Translat',
        'descCn' => 'IPv4转IPv6地址'
    ],
    [
        'scope' => '64:ff9b:1::/48',
        'range' => '64:ff9b:1:: - 64:ff9b:1:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'IPv4-IPv6 Translat',
        'descCn' => 'IPv4转IPv6地址'
    ],
    [
        'scope' => '100::/64',
        'range' => '100:: - 100::ffff:ffff:ffff:ffff',
        'descEn' => 'Discard-Only Address Block',
        'descCn' => '仅丢弃块地址'
    ],
    [
        'scope' => '2001:1::1/128',
        'range' => '2001:1::1 - 2001:1::1',
        'descEn' => 'Port Control Protocol Anycast',
        'descCn' => '端口控制协议任播地址'
    ],
    [
        'scope' => '2001:1::2/128',
        'range' => '2001:1::2 - 2001:1::2',
        'descEn' => 'Traversal Using Relays around NAT Anycast',
        'descCn' => '中继遍历NAT任播地址'
    ],
    [
        'scope' => '2001:2::/48',
        'range' => '2001:2:: - 2001:2::ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'Benchmarking',
        'descCn' => '基准测试地址'
    ],
    [
        'scope' => '2001:3::/32',
        'range' => '2001:3:: - 2001:3:ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'AMT',
        'descCn' => 'AMT地址'
    ],
    // [
    //     'scope' => '2001:4:112::/48',
    //     'range' => '2001:4:112:: - 2001:4:112:ffff:ffff:ffff:ffff:ffff',
    //     'descEn' => 'AS112-v6',
    //     'descCn' => ''
    // ],
    [
        'scope' => '2001:10::/28',
        'range' => '2001:10:: - 2001:1f:ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'Deprecated (previously ORCHID)',
        'descCn' => 'ORCHID地址（已弃用）'
    ],
    [
        'scope' => '2001:20::/28',
        'range' => '2001:20:: - 2001:2f:ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'ORCHIDv2',
        'descCn' => 'ORCHIDv2地址'
    ],
    [
        'scope' => '2001::/23',
        'range' => '2001:: - 2001:1ff:ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'IETF Protocol Assignments',
        'descCn' => 'IETF协议分配地址'
    ],
    [
        'scope' => '2001:db8::/32',
        'range' => '2001:db8:: - 2001:db8:ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'Documentation',
        'descCn' => '文档地址'
    ],
    [
        'scope' => '2001::/32',
        'range' => '2001:: - 2001::ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'TEREDO',
        'descCn' => 'TEREDO地址'
    ],
    [
        'scope' => '2002::/16',
        'range' => '2002:: - 2002:ffff:ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'IPv6 to IPv4',
        'descCn' => 'IPv6转IPv4地址'
    ],
    // [
    //     'scope' => '2620:4f:8000::/48',
    //     'range' => '2620:4f:8000:: - 2620:4f:8000:ffff:ffff:ffff:ffff:ffff',
    //     'descEn' => 'Direct Delegation AS112 Service',
    //     'descCn' => ''
    // ],
    [
        'scope' => 'fc00::/7',
        'range' => 'fc00:: - fdff:ffff:ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'Unique-Local',
        'descCn' => '唯一本地地址'
    ],
    [
        'scope' => 'fe80::/10',
        'range' => 'fe80:: - febf:ffff:ffff:ffff:ffff:ffff:ffff:ffff',
        'descEn' => 'Link-Local Unicast',
        'descCn' => '链路本地地址'
    ],
);

function cidrToRange($cidr) { // CIDR转IP段
    $cidr = explode('/', $cidr);
    $range['start'] = long2ip((ip2long($cidr[0])) & ((-1 << (32 - (int)$cidr[1]))));
    $range['end'] = long2ip((ip2long($range['start'])) + pow(2, (32 - (int)$cidr[1])) - 1);
    return $range;
}

function ip2long6($ipv6) { // 压缩IPv6地址为long
    $ip_n = inet_pton($ipv6);
    $bits = 15;
    while ($bits >= 0) {
      $bin = sprintf("%08b", (ord($ip_n[$bits])));
      $ipv6long = $bin.$ipv6long;
      $bits--;
    }
    return gmp_strval(gmp_init($ipv6long, 2), 10);
}
  
function long2ip6($ipv6long) { // 解压long为IPv6地址
    $bin = gmp_strval(gmp_init($ipv6long, 10), 2);
    if (strlen($bin) < 128) {
        $pad = 128 - strlen($bin);
        for ($i = 1; $i <= $pad; $i++) {
            $bin = '0' . $bin;
        }
    }
    $bits = 0;
    while ($bits <= 7) {
        $bin_part = substr($bin, ($bits * 16), 16);
        $ipv6 .= dechex(bindec($bin_part)) . ':';
        $bits++;
    }
    return inet_ntop(inet_pton(substr($ipv6, 0, -1)));
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
    global $specialIPv6;
    $ipv6 = ip2long6($ip);
    foreach ($specialIPv6 as $special) {
        $range = explode(' - ', $special['range']);
        if ($ipv6 >= ip2long6($range[0]) && $ipv6 <= ip2long6($range[1])) {
            unset($special['range']);
            return $special;
        }
    }
    return null; // 非特殊地址
}

function checkSpecial($ip) { // 检查特殊IP地址并返回说明
    if (filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) { // IPv4
        return checkSpecialIPv4($ip);
    } else {
        return checkSpecialIPv6($ip);
    }
}

?>