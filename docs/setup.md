## 常规部署方式

此方式涉及服务较多，配置较为繁琐且可能存在版本兼容问题，不熟悉Linux操作的用户建议使用[Docker方式](./setup-docker.md)。

### 1. 拉取源码

首先拉取仓库到服务器上，这里以 `/var/www/echoIP` 为例

```
shell> cd /var/www
shell> git clone https://github.com/dnomd343/echoIP.git
Cloning into 'echoIP'...
···
Unpacking objects: 100% ··· done.
```

### 2. 环境检查

确定你的服务器上有PHP环境、Node.js环境，同时有 `curl` 与 `wget` 工具

```
shell> php -v
···PHP版本信息···

shell> node -v
···Nodejs版本信息···

shell> curl --version
···curl版本信息···

shell> wget --version
···wget版本信息···
```

确认PHP-FPM正常运行

```
shell> systemctl | grep fpm
  php7.3-fpm.service            loaded active running   The PHP 7.3 FastCGI Process Manager
```

确认Redis正常运行

```
shell> redis-cli --version
···Redis版本信息···

# 登录redis服务
shell> redis-cli
# 若服务主机非默认参数，使用以下命令登录
shell> redis-cli -h {hostname} -p {port}

# 若配置有密码则先认证
127.0.0.1:6379> auth {passwd}

# 登录后确认连接
127.0.0.1:6379> ping
PONG
```

### 3. qqwry.dat配置

获取并解密纯真IP数据库

```
shell> cd /var/www/echoIP/backend
# 运行升级脚本
shell> sh qqwryUpdate.sh
···
qqwry.dat update complete.
```

开启数据解析服务

```
shell> cd /var/www/echoIP/backend/qqwryFormat
# 默认端口为1602，注意不要重复开启
shell> ./start.sh
```

### 4. 配置Redis连接

Redis连接参数位于 `backend/redis.php` 文件中，默认如下

```
$redisSetting = array(
    'enable' => true,
    'host' => '127.0.0.1',
    'port' => 6379,
    'passwd' => '',
    'prefix' => 'echoip-',
    'cache_time' => 3600000
);
```

按当前服务器配置修改，`enable` 为false时可关闭缓存功能，无密码时将 `passwd` 留空即可，键值前缀与缓存时间（单位ms）按实际需要修改。


### 5. 配置Web服务

配置网页服务器代理，需要额外占用除80与443之外的一个端口，默认为TCP/1601，可按需修改。这里使用Nginx作为示例，其他Web服务原理类似。

```
# 进入nginx配置目录
shell> cd /etc/nginx/conf.d

# 从代码仓库复制配置文件
shell> cp /var/www/echoIP/conf/nginx/ip.conf ./

# 修改配置文件中域名、证书、端口等信息
shell> vim ip.conf
```

配置文件内容如下

```
server {
    listen 80;
    listen [::]:80;
    server_name ip.343.re; # 改为自己的域名
    location / {
        if ($http_user_agent !~* (curl|wget)) {
            return 301 https://$server_name$request_uri;
        }
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_pass http://127.0.0.1:1601;
    }
}

server {
    listen 443 ssl http2;
    listen [::]:443 ssl http2;
    server_name ip.343.re; # 改为自己的域名
    ssl_certificate /etc/ssl/certs/343.re/fullchain.pem; # 改为自己的证书
    ssl_certificate_key /etc/ssl/certs/343.re/privkey.pem;
    
    gzip on;
    gzip_buffers 32 4K;
    gzip_comp_level 6;
    gzip_min_length 100;
    gzip_types application/javascript text/css text/xml;
    gzip_disable "MSIE [1-6]\.";
    gzip_vary on;

    location / {
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_pass http://127.0.0.1:1601;
    }
}

server {
    listen 127.0.0.1:1601;
    set $my_host 127.0.0.1:1601;
    set_real_ip_from 0.0.0.0/0;
    real_ip_header X-Real-IP;

    root /var/www/echoIP;
    error_page 403 404 = /error.html;

    location ^~ /assets {}
    location = /index.html {}
    location = /error.html {}

    location = /error {
        index error.html;
    }

    location = /ua {
        if ($http_user_agent ~* (curl|wget)) {
            return 200 $http_user_agent\n;
        }
        default_type application/json;
        return 200 $http_user_agent;
    }

    location = / {
        set $query_param ?justip=true&cli=true;
        if ($http_user_agent ~* (curl|wget)) {
            proxy_pass http://$my_host/query$query_param;
        }
        index index.html;
    }

    location / {
        set $query_param $query_string;
        if ($http_user_agent ~* (curl|wget)) {
            set $query_param $query_param&cli=true;
        }
        include fastcgi_params;
        fastcgi_pass 127.0.0.1:9000; # 服务器php-fpm接口
        fastcgi_param QUERY_STRING $query_param;
        fastcgi_param SCRIPT_FILENAME /var/www/echoIP/backend/queryInfo.php;
    }
}
```

其中PHP-FPM接口在各系统上不同

```
# RH系一般为本地9000端口
shell> netstat -tlnp | grep 9000
tcp        0      0 127.0.0.1:9000          0.0.0.0:*               LISTEN      783/php-fpm: master
# Debian系一般为sock方式
shell> ls /var/run/php/
php7.3-fpm.pid  php7.3-fpm.sock
```

对应Nginx配置如下
```
# RH系
fastcgi_pass 127.0.0.1:9000;
# Debian系
fastcgi_pass unix:/var/run/php/php7.3-fpm.sock;
```

重启Nginx服务

```
shell> nginx -s reload
```

将配置的域名DNS解析到当前服务器，即可用该域名访问echoIP服务。