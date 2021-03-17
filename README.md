# echoIP

> 显示客户端IP、查询IP详细信息

结合 [ipinfo.io](https://ipinfo.io/)、[IPIP.net](https://www.ipip.net/)、[纯真IP](http://www.cz88.net/) 的数据计算指定IP信息，支持IPv4与IPv6地址。

客户端可直接向服务器询问自己的IP地址，同时可指定任意IP地址获取其详细信息。

## 使用方法

### 命令行模式

```
# 查询自己的IP
shell> curl ip.343.re
···

# 查询自己IP的详细信息
shell> curl ip.343.re/info
···

# 查询指定IP地址详细信息
shell> curl ip.343.re/8.8.8.8
···
```

![echoIP-cli](https://pic.dnomd343.top/images/aDE.png)

### 网页访问模式

你可以直接在 [ip.343.re](https://ip.343.re/) 进行查询，或者将项目部署到自己的服务器上。

![echoIP-web](https://pic.dnomd343.top/images/k2H.png)

## 如何部署

### 常规方式

首先拉取仓库到你的服务器上，这里以 `/var/www/echoIP` 为例

```
shell> cd /var/www
shell> git clone https://github.com/dnomd343/echoIP.git
Cloning into 'echoIP'...
···
Unpacking objects: 100% ··· done.
```

确定你的服务器上有PHP环境，同时有 `curl` 与 `wget` 工具

```
shell> php -v
···PHP版本信息···

shell> curl --version
···curl版本信息···

shell> wget --version
···wget版本信息···
```

配置网页服务器代理，这里以Nginx为例

```
# 进入nginx配置目录
shell> cd /etc/nginx/conf.d
shell> vim ip.conf
···
shell> vim ip.func
···
```

写入配置文件

`/etc/nginx/conf.d/ip.conf`

```
server {
    listen 80;
    server_name ip.343.re; # 改为自己的域名
    include conf.d/ip.func;
}

server {
    listen 443 ssl http2;
    server_name ip.343.re; # 改为自己的域名
    ssl_certificate /etc/ssl/certs/343.re/fullchain.pem; # 改为自己的证书
    ssl_certificate_key /etc/ssl/certs/343.re/privkey.pem;
    include conf.d/ip.func;
}
```

`/etc/nginx/conf.d/ip.func`

```
root /var/www/echoIP;

location = / {
    if ($http_user_agent ~* (curl|wget)) {
        return 200 $remote_addr\n;
    }
    if ($scheme = http) {
        return 301 https://$server_name;
    }
    index index.html;
}

location = /ua {
    if ($http_user_agent ~* (curl|wget)) {
        return 200 $http_user_agent\n;
    }
    default_type application/json;
    return 200 $http_user_agent;
}

location = /ip {
    if ($http_user_agent ~* (curl|wget)) {
        return 200 $remote_addr\n;
    }
    if ($scheme = http) {
        return 301 https://$server_name/ip;
    }
    return 200 $remote_addr;
}

location ~* ^/([^/]+?)$ {
    set $request_ip $1;
    if ($http_user_agent ~* (curl|wget)) {
        proxy_pass https://ip.343.re/info/$request_ip; # 改成自己的域名
        break;
    }
    if ($scheme = http) {
        return 301 https://$server_name$request_uri;
    }
}

location ^~ /info {
    set $is_cli 0;
    set $is_https 0;
    set $is_legal 0;
    if ($uri ~* ^/info/?$) {
        set $is_legal 1;
        set $query ip=$remote_addr;
    }
    if ($uri ~* ^/info/([^/]+?)$) {
        set $is_legal 1;
        set $query ip=$1;
    }
    if ($is_legal = 0) {
        return 404;
    }
    if ($scheme = https) {
        set $is_https 1;
    }
    if ($http_user_agent ~* (curl|wget)) {
        set $is_cli 1;
        set $query $query&cli=true;
    }
    set $flag_https_cli $is_https$is_cli;
    if ($flag_https_cli = 00) {
        return 301 https://$server_name$request_uri;
    }
    include fastcgi_params;
    fastcgi_pass 127.0.0.1:9000;
    fastcgi_param QUERY_STRING $query;
    fastcgi_param SCRIPT_FILENAME /var/www/echoIP/backend/queryInfo.php;
}
```

重启Nginx服务

```
shell> nginx -s reload
```

### Docker方式

待补充...

## 许可证

MIT [@dnomd343](https://github.com/dnomd343) [@ShevonKuan](https://github.com/ShevonKuan)
