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

待补充...

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

配置网页服务器代理，这里提供[Nginx示例](https://github.com/dnomd343/echoIP/blob/main/conf/nginx/README.md)

## 开发资料

### ipinfo.io

待补充...

### IPIP.net

待补充...

### 纯真IP库

待补充...

## 许可证

MIT ©2021 [@dnomd343](https://github.com/dnomd343) [@ShevonKuan](https://github.com/ShevonKuan)
