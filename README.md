# echoIP

> 显示客户端IP、查询IP详细信息

结合 [ipinfo.io](https://ipinfo.io/)、[IPIP.net](https://www.ipip.net/)、[纯真IP](http://www.cz88.net/) 的数据获取IP地址的信息，支持IPv4与IPv6地址。

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

![echoIP-web](https://pic.dnomd343.top/images/Wg7.png)

## 如何部署

> 如果想在自己域名下建立本服务，可按如下方式部署

### Docker方式

确定你的服务器上有Docker环境

```
shell> docker -v
···Docker版本信息···
```

启动容器并映射端口

```
# 映射容器服务到宿主机1601端口
shell> docker run -dit --name echoip -p 1601:8080 dnomd343/echoip
```

测试容器是否正常工作

```
shell> curl 127.0.0.1:1601/8.8.8.8
···8.8.8.8的详细信息···
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
    server_name ip.343.re; # 改为自己的域名
    location / {
        if ($http_user_agent !~* (curl|wget)) {
            return 301 https://$server_name$request_uri;
        }
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
    location / {
        proxy_set_header X-Real-IP $remote_addr;
        proxy_pass http://127.0.0.1:1601;
    }
}
```

重启Nginx服务

```
shell> nginx -s reload
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
shell> ./start.sh
```

配置网页服务器代理，需要额外占用除80与443之外的一个端口，默认为TCP/1601，可按需修改

```
# 进入nginx配置目录
shell> cd /etc/nginx/conf.d

# 从代码仓库复制配置文件
shell> cp /var/www/echoIP/conf/nginx/ip.conf ./

# 修改配置文件，将ip.343.re改为需要部署的域名
shell> vim ip.conf
···
```

重启Nginx服务

```
shell> nginx -s reload
```

## 开发资料

### Docker容器

制作echoIP镜像

```
shell> docker build -t echoip https://github.com/dnomd343/echoIP.git#main
```

启动容器

```
shell> docker run -dit --name echoip -p 1601:8080 echoip
```

进入容器调试

```
shell> docker exec -it echoip bash
```

### ipinfo.io

在线请求，格式为 `https://ipinfo.io/$IP/json`，返回指定IP对应的信息，形如：

```
{
  "ip": "47.242.30.65",
  "city": "Kowloon",
  "region": "Kowloon City",
  "country": "HK",
  "loc": "22.3167,114.1833",
  "org": "AS45102 Alibaba (US) Technology Co., Ltd.",
  "timezone": "Asia/Hong_Kong",
  "readme": "https://ipinfo.io/missingauth"
}
```

查询代码位于 `backend/ipinfo.php`

### IPIP.net

离线数据库，在 [官网](https://www.ipip.net/product/ip.html) 登录后即可下载，国内可精确到市，格式为ipdb，数据不定期更新。

数据库文件位于 `backend/ipipfree.ipdb`， 查询代码位于 `backend/ipip.php`

### 纯真IP库

离线数据库，获取方式及解码原理可以参考[这篇](https://blog.dnomd343.top/qqwry.dat-analyse/)博客，国内定位精度较高，数据每5天更新一次。

数据库文件位于 `backend/qqwry.dat`，数据库更新脚本位于 `backend/qqwryUpdate.sh`，查询代码位于 `backend/qqwry.php`，数据解析服务位于 `backend/qqwryFormat/*`

## 许可证

MIT ©2021 [@dnomd343](https://github.com/dnomd343) [@ShevonKuan](https://github.com/ShevonKuan)
