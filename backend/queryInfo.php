<?php

include("getInfo.php");

$ip = $_GET['ip'];

if (!filter_var($ip, \FILTER_VALIDATE_IP)) {
    echo "Illegal IP format".PHP_EOL;
    exit;
}

echo getIPInfo($ip);

?>
