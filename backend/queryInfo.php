<?php

include("qrcode.php");
include("getInfo.php");

function getClientIP() { // 获取客户端IP
    return isset($_SERVER['HTTP_X_FORWARDED_FOR']) ? $_SERVER['HTTP_X_FORWARDED_FOR'] : $_SERVER['REMOTE_ADDR'];
}

function formatDate($str) { // 将YYYYMMDD处理为YYYY-MM-DD
    return substr($str, 0, 4) . '-' . substr($str, 4, 2) . '-' . substr($str, 6, 2);
}

function errorPage() { // 跳转到错误页
    header('HTTP/1.1 302 Moved Temporarily');
    header('Location: /error');
}

function getQrCode($str, $block) { // 用自定义字符绘制二维码
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

function getQrCodeUtf($str) { // 用特殊Unicode编码绘制二维码
    $qr = QRCode::getMinimumQRCode($str, QR_ERROR_CORRECT_LEVEL_L);
    $length = $qr->getModuleCount();
    for ($y = 0; $y < $length; $y++) {
        for ($x = 0; $x < $length; $x++) {
            $table[$y][$x] = $qr->isDark($y, $x);
        }
        if ($length % 2) {
            $table[$y][$length] = false; // 宽度扩充为偶数
        }
    }
    if ($length % 2) { // 若二维码边长为奇数
        for ($i = 0; $i <= $length; $i++) {
            $table[$length][$i] = false; // 高度扩充为偶数
        }
        $length++;
    }
    for ($y = 0; $y < $length; $y += 2) { // 每次输出两行
        for ($x = 0; $x < $length; $x++) {
            if ($table[$y][$x] && $table[$y + 1][$x]) { // 分四种情况输出上下两格
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
    $requestUri = $_SERVER['DOCUMENT_URI']; // 获取不带参数的请求路径
    if ($_GET['cli'] == 'true') { // 识别nginx附带的cli参数
        $request['cli'] = true;
    }
    if ($requestUri == '/' || $requestUri == '/ip') { // URI -> / or /ip
        $request['justip'] = true;
        return;
    } else if ($requestUri == '/help') { // URI -> /help
        $request['help'] = true;
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
        if ($_GET['help'] == 'true') { $request['help'] = true; }
        if ($_GET['gbk'] == 'true') { $request['gbk'] = true; }
        if ($_GET['qr'] == 'true') { $request['qr'] = true; }
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
    $request['error'] = true; // 未匹配到请求路径
}

function getInfo($ip) { // 获取并格式化IP数据
    global $request;
    $info = getIPInfo($ip);
    if ($request['cli']) { // 使用命令行模式
        $cli = "IP: " . $info['ip'] . PHP_EOL;
        if ($info['as'] != NULL) { $cli .= "AS: " . $info['as'] . PHP_EOL; }
        if ($info['city'] != NULL) { $cli .= "City: " . $info['city'] . PHP_EOL; }
        if ($info['region'] != NULL) { $cli .= "Region: " . $info['region'] . PHP_EOL; }
        if ($info['country'] != NULL) { $cli .= "Country: " . $info['country'] . PHP_EOL; }
        if ($info['timezone'] != NULL) { $cli .= "Timezone: " . $info['timezone'] . PHP_EOL; }
        if ($info['loc'] != NULL) { $cli .= "Location: " . $info['loc'] . PHP_EOL; }
        if ($info['isp'] != NULL) { $cli .= "ISP: " . $info['isp'] . PHP_EOL; }
        if ($info['scope'] != NULL) { $cli .= "Scope: " . $info['scope'] . PHP_EOL; }
        if ($info['detail'] != NULL) { $cli .= "Detail: " . $info['detail'] . PHP_EOL; }
        return $cli;
    }
    $info['status'] = 'T';
    header('Content-Type: application/json; charset=utf-8'); // 以JSON格式发送
    return json_encode($info);
}

function routeParam() {
    // error -> 请求出错
    // version -> 获取版本数据
    // help -> 显示帮助信息
    // cli -> 来自命令行下的请求
    // gbk -> 返回数据使用GBK编码
    // qr -> 生成二维码
    // qr_fill -> 二维码填充符号
    // justip -> 仅查询IP地址
    // ip -> 请求指定IP的数据

    global $request;
    global $webUri;
    global $helpContent;
    if ($request['error']) { // 请求出错
        if ($request['cli']) { // 命令行模式
            echo 'Illegal Request' . PHP_EOL;
        } else {
            errorPage();
        }
        exit; // 退出
    }

    if ($request['help']) { // 显示帮助信息
        if ($request['cli']) {
            echo $helpContent;
        } else {
            errorPage(); // 网页模式不输出
        }
        exit;
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
        if ($request['cli']) {
            echo $webUri . '?ip=' . getClientIP() . PHP_EOL;
            if (isset($request['qr_fill'])) { // 使用字符填充生成二维码
                echo getQrCode($webUri . '?ip=' . getClientIP(), $request['qr_fill']);
            } else { // 使用特殊Unicode字符生成二维码
                echo getQrCodeUtf($webUri . '?ip=' . getClientIP());
            }
        } else {
            errorPage(); // 网页模式不输出
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
    $info = getInfo($ip); // 查询目标IP
    if ($request['gbk']) {
        $info = iconv('UTF-8', 'gbk', $info); // 输出为GBK编码
    }
    echo $info;
}

function main() {
    preRount(); // 解析请求路径
    routeParam(); // 处理请求参数
}

$myVersion = 'v1.3';

$request = array(
    'error' => false,
    'version' => false,
    'help' => false,
    'cli' => false,
    'gbk' => false,
    'qr' => false,
    'justip' => false
);

$webSite = 'ip.343.re'; // 默认域名
if (isset($_SERVER['HTTP_HOST'])) {
    preg_match('#^127.0.0.1#', $_SERVER['HTTP_HOST'], $match); // 排除127.0.0.1下的host
    if (count($match) == 0) {
        $webSite = $_SERVER['HTTP_HOST'];
    }
}
$webUri = 'http://' . $webSite . '/';

$helpContent = PHP_EOL . 'echoIP - ' . $myVersion . ' (https://github.com/dnomd343/echoIP)' . PHP_EOL . '
Format: http(s)://' . $webSite . '{Request_URI}

    / or /ip -> Show client IP.

    /info or /info/ -> Show detail of client IP.
    /{ip} or /info/{ip} -> Show detail of {ip}.

    /info/gbk -> Show detail of client IP (use GBK encoding).
    /{ip}/gbk or /info/{ip}/gbk -> Show detail of {ip} (use GBK encoding).

    /qr -> Show QR code of client IP (use special unicode characters).
    /qr/ -> Show QR code of client IP (use full characters).
    /qr/{xx} -> Show QR code of client IP (Use two custom characters).

    /help -> Show help message.
    /ua -> Show http user-agent of client.
    /version -> Show version of echoIP and IP database.
    
    /query?xxx=xxx&xxx=xxx
       |-> error=true: Show error request.
       |-> version=true: Show help message.
       |-> help=true: Show version of echoIP and IP database.
       |-> gbk=true: Use GBK encoding.
       |-> qr=true: Show QR code of client IP.
       |-> justip=true: Only query the client IP.
       |-> ip={ip}: Query of specified IP.

';

main();

?>