# echoIP

> 显示客户端IP、查询IP详细信息

结合 [ipinfo.io](https://ipinfo.io/)、[IPIP.net](https://www.ipip.net/)、[纯真IP](http://www.cz88.net/) 的数据计算指定IP信息，支持IPv4与IPv6地址。

客户端可直接向服务器询问自己的IP地址，同时可指定任意IP地址获取其详细信息。

## 如何使用

### 命令行模式

```
# 查询客户端IP
shell> curl ip.343.re
···

# 查询客户端UA
shell> curl ip.343.re/ua
···

# 查询客户端IP的详细信息
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

> 若你想用自己的域名建立一个类似的服务，可按如下方式部署

### Docker方式

确定你的服务器上有Docker环境

```
shell> docker -v
···Docker版本信息···
```

启动容器并映射端口

```
# 这里映射到宿主机1601端口，可更改
shell> docker run -dit --name echoip -p 1601:8080 dnomd343/echoip
```

配置Nginx反向代理

```
# 进入Nginx配置目录
shell> cd /etc/nginx/conf.d
shell> vim ip.conf
···
```

写入配置文件

```
server {
    listen 80;
    listen [::]:80;
    server_name ip.343.re; # 
    location / {
        if ($http_user_agent !~* (curl|wget)) {
            return 301 https://$server_name$request_uri;
        }
        proxy_set_header X-Real-IP $remote_addr;
        proxy_pass http://127.0.0.1:1601;
    }
}

server {
    listen 444 ssl http2 proxy_protocol;
    listen [::]:444 ssl http2 proxy_protocol;
    server_name ip.343.re;
    ssl_certificate /etc/ssl/certs/343.re/fullchain.pem;
    ssl_certificate_key /etc/ssl/certs/343.re/privkey.pem;
    location / {
        proxy_set_header X-Real-IP $proxy_protocol_addr;
        proxy_pass http://127.0.0.1:1601;
    }
}
```

### 常规方式

首先拉取仓库到你的服务器上，这里以 `/var/www/echoIP` 为例

```
shell> cd /var/www
shell> git clone https://github.com/dnomd343/echoIP.git
Cloning into 'echoIP'...
···
Unpacking objects: 100% ··· done.
```

确定你的服务器上有PHP环境、Nodejs环境，同时有 `curl` 与 `wget` 工具

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

开启纯真IP库解析服务

```
shell> cd /var/www/echoIP/backend/qqwryFormat
# 默认端口为1602，注意不要重复开启
shell> ./start
```

配置网页服务器代理，这里提供[Nginx示例](https://github.com/dnomd343/echoIP/blob/main/conf/nginx/README.md)

## 开发资料

### Docker容器

制作echoIP镜像

```
shell> docker build -t echoip https://github.com/dnomd343/echoIP.git#main
···
```

启动容器

```
shell> docker run -dit --name echoip -p 1601:8080 echoip
···
```

进入容器调试

```
shell> docker exec -it echoip bash
···
```

### ipinfo.io

待补充...

### IPIP.net

待补充...

### 纯真IP库

待补充...

## 许可证

> IPIP.net免费库、纯真IP库均不可商用

MIT ©2021 [@dnomd343](https://github.com/dnomd343) [@ShevonKuan](https://github.com/ShevonKuan)
