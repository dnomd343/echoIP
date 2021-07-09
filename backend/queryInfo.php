<?php

include("qrcode.php");
include("getInfo.php");

function getClientIP() { // 获取客户端IP
    return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
}

function formatDate($str) { // 将YYYYMMDD处理为YYYY-MM-DD
    return substr($str, 0, 4) . '-' . substr($str, 4, 2) . '-' . substr($str, 6, 2);
}

function getQrCode($str, $block) {
    $qrString = '';
    $qr = QRCode::getMinimumQRCode($str, QR_ERROR_CORRECT_LEVEL_L);
    for ($y = 0; $y < $qr->getModuleCount(); $y++) {
        for ($x = 0; $x < $qr->getModuleCount(); $x++) {
            $qrString .= ($qr->isDark($y, $x) ? $block : '  ');
        }
        $qrString .= PHP_EOL;
    }
    return $qrString;
}

function getQrCodeUtf($str) {
    $qr = QRCode::getMinimumQRCode($str, QR_ERROR_CORRECT_LEVEL_L);
    $length = $qr->getModuleCount();
    for ($y = 0; $y < $length; $y++) {
        for ($x = 0; $x < $length; $x++) {
            $table[$y][$x] = $qr->isDark($y, $x);
        }
        if ($length % 2) {
            $table[$y][$length] = false;
        }
    }
    if ($length % 2) {
        for ($i = 0; $i <= $length; $i++) {
            $table[$length][$i] = false;
        }
        $length++;
    }
    for ($y = 0; $y < $length; $y += 2) {
        for ($x = 0; $x < $length; $x++) {
            if ($table[$y][$x] && $table[$y + 1][$x]) {
                echo '█';
            } else if ($table[$y][$x] && !$table[$y + 1][$x]) {
                echo '▀';
            } else if (!$table[$y][$x] && $table[$y + 1][$x]) {
                echo '▄';
            } else {
                echo ' ';
            }
        }
        echo PHP_EOL;
    }
}

function preRount() { // 解析请求路径
    global $request;
    $requestUri = $_SERVER['DOCUMENT_URI'];
    if ($_GET['cli'] == 'true') {
        $request['cli'] = true;
    }
    if ($requestUri == '/' || $requestUri == '/ip') { // URI -> / or /ip
        $request['justip'] = true;
        return;
    } else if ($requestUri == '/version') { // URI -> /version
        $request['version'] = true;
        return;
    } else if ($requestUri == '/qr') { // URI -> /qr
        $request['qr'] = true;
        return;
    } else if ($requestUri == '/qr/') { // URI -> /qr/
        $request['qr'] = true;
        $request['qr_fill'] = '██';
        return;
    } else if ($requestUri == '/info' || $requestUri == '/info/') { // URI -> /info or /info/
        $request['ip'] = getClientIP();
        return;
    } else if ($requestUri == '/info/gbk') { // URI -> /info/gbk
        $request['ip'] = getClientIP();
        $request['gbk'] = true;
        return;
    } else if ($requestUri == '/query') { // URI -> /query?xxx=xxx
        if ($_GET['error'] == 'true') { $request['error'] = true; }
        if ($_GET['version'] == 'true') { $request['version'] = true; }
        if ($_GET['gbk'] == 'true') { $request['gbk'] = true; }
        if ($_GET['justip'] == 'true') { $request['justip'] = true; }
        if (isset($_GET['ip'])) { $request['ip'] = $_GET['ip']; }
        return;
    }
    preg_match('#^/qr/([^/]{2})$#', $requestUri, $match); // URI -> /qr/{qr_fill}
    if (count($match) > 0) {
        $request['qr'] = true;
        $request['qr_fill'] = $match[1];
        return;
    }
    preg_match('#^/([^/]+?)$#', $requestUri, $match); // URI -> /{ip}
    if (count($match) > 0) {
        if ($request['cli']) { // 命令行模式
            $request['ip'] = $match[1];
        } else {
            $request['error'] = true;
        }
        return;
    }
    preg_match('#^/([^/]+?)/gbk$#', $requestUri, $match); // URI -> /{ip}/gbk
    if (count($match) > 0) {
        $request['ip'] = $match[1];
        $request['gbk'] = true;
        return;
    }
    preg_match('#^/info/([^/]+?)$#', $requestUri, $match); // URI -> /info/{ip}
    if (count($match) > 0) {
        $request['ip'] = $match[1];
        return;
    }
    preg_match('#^/info/([^/]+?)/gbk$#', $requestUri, $match); // URI -> /info/{ip}/gbk
    if (count($match) > 0) {
        $request['ip'] = $match[1];
        $request['gbk'] = true;
        return;
    }
    $request['error'] = true;
}

function routeParam() {
    // error -> 请求出错
    // version -> 获取版本数据
    // cli -> 来自命令行下的请求
    // gbk -> 返回数据使用GBK编码
    // qr -> 生成二维码
    // qr_fill -> 二维码填充符号
    // justip -> 仅查询IP地址
    // ip -> 请求指定IP的数据

    global $request;
    global $webUri;
    if ($request['error']) { // 请求出错
        if ($request['cli']) { // 命令行模式
            echo 'Illegal Request' . PHP_EOL;
        } else {
            header('HTTP/1.1 302 Moved Temporarily');
            header('Location: /error');
        }
        exit; // 退出
    }

    if ($request['version']) { // 请求版本信息
        $version = getVersion();
        if ($request['cli']) { // 命令行模式
            echo "echoip -> " . $version['echoip'] . PHP_EOL;
            echo "qqwry.dat -> " . formatDate($version['qqwry']) . PHP_EOL;
            echo "ipip.net -> " . formatDate($version['ipip']) . PHP_EOL;
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($version); // 返回JSON数据
        }
        exit; // 退出
    }

    if ($request['qr']) { // 生成二维码
        if (!$request['cli']) { // 网页模式不输出
            header('HTTP/1.1 302 Moved Temporarily');
            header('Location: /error');
        } else {
            echo $webUri . PHP_EOL;
            if (isset($request['qr_fill'])) { // 使用字符填充生成二维码
                echo getQrCode($webUri . '?ip=' . getClientIP(), $request['qr_fill']);
            } else { // 使用特殊Unicode字符生成二维码
                echo getQrCodeUtf($webUri . '?ip=' . getClientIP());
            }
        }
        exit;
    }

    if ($request['justip']) { // 仅查询IP地址
        if ($request['cli']) { // 命令行模式
            echo getClientIP() . PHP_EOL;
        } else {
            header('Content-Type: application/json; charset=utf-8');
            echo '{"ip":"' . getClientIP() . '"}'; // 返回JSON数据
        }
        exit;
    }

    $ip = isset($request['ip']) ? $request['ip'] : getClientIP(); // 若存在请求信息则查询该IP
    if (!filter_var($ip, \FILTER_VALIDATE_IP)) { // 输入IP不合法
        if ($request['cli']) { // 命令行模式
            echo "Illegal Request" . PHP_EOL;
        } else {
            $reply = array(
                'status' => 'F',
                'message' => 'Illegal Request'
            );
            header('Content-Type: application/json; charset=utf-8');
            echo json_encode($reply);
        }
        exit;
    }
    $info = getIPInfo($ip); // 查询目标IP
    if ($request['gbk']) {
        $info = iconv('UTF-8', 'gbk', $info);
    }
    echo $info;
}

function main() {
    preRount(); // 解析请求路径
    routeParam(); // 处理请求参数
}

$myVersion = 'v1.2';

$request = array(
    'error' => false,
    'version' => false,
    'cli' => false,
    'gbk' => false,
    'qr' => false,
    'justip' => false
);

$webUri = 'ip.343.re';
if (isset($_SERVER['HTTP_HOST'])) {
    preg_match('#^127.0.0.1#', $_SERVER['HTTP_HOST'], $match); // 排除127.0.0.1下的host
    if (count($match) == 0) {
        $webUri = $_SERVER['HTTP_HOST'];
    }
}
$webUri = 'http://' . $webUri . '/';

main();

?>