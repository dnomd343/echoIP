## Nginx配置

### 方式A

需要两个配置文件 `ip.conf` 和 `ip-sub.func`

```
# 进入nginx配置目录
shell> cd /etc/nginx/conf.d

# 从代码仓库复制配置文件
shell> cp /var/www/echoIP/conf/nginx/methodA/ip.conf ./
shell> cp /var/www/echoIP/conf/nginx/methodB/ip-sub.func ./

# 修改配置文件，将ip.343.re改为需要部署的域名
shell> vim ip.conf
···
shell> vim ip-sub.func
···
```

重启Nginx服务

```
shell> nginx -s reload
```

### 方式B

需要一个配置文件 `ip.conf` ，但需要额外占用除80与443之外的一个端口，默认为TCP/1601，可按需修改

```
# 进入nginx配置目录
shell> cd /etc/nginx/conf.d

# 从代码仓库复制配置文件
shell> cp /var/www/echoIP/conf/nginx/methodB/ip.conf ./

# 修改配置文件，将ip.343.re改为需要部署的域名
shell> vim ip.conf
···
```

重启Nginx服务

```
shell> nginx -s reload
```