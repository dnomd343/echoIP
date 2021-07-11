<?php

$redisSetting = array(
    'enable' => true,
    'host' => '127.0.0.1',
    'port' => 6379,
    'passwd' => '',
    'prefix' => 'echoip-',
    'cache_time' => 3600000
);

function getRedisData($ip) { // 查询Redis，不存在返回NULL
    $redis = new Redis();
    global $redisSetting;
    $redis->connect($redisSetting['host'], $redisSetting['port']);
    $redis->auth($redisSetting['passwd']);
    $redisKey = $redisSetting['prefix'] . $ip;
    $redisValue = $redis->exists($redisKey) ? $redis->get($redisKey) : NULL;
    return $redisValue;
}

function setRedisData($ip, $data) { // 写入信息到Redis
    $redis = new Redis();
    global $redisSetting;
    $redis->connect($redisSetting['host'], $redisSetting['port']);
    $redis->auth($redisSetting['passwd']);
    $redisKey = $redisSetting['prefix'] . $ip;
    $redis->set($redisKey, $data); // 写入数据库
    $redis->pexpire($redisKey, $redisSetting['cache_time']); // 设置过期时间
}

?>