## Docker部署方式

echoIP支持Docker容器部署，在[Docker Hub](https://hub.docker.com/repository/docker/dnomd343/echoip)可获取已构建的镜像。

### 1. 配置Docker环境

使用以下命令确认Docker环境

```
# 若正常输出则跳过本步
shell> docker --version
···Docker版本信息···
```

使用以下命令安装Docker

```
# RH系
shell> sudo yum update
···
# Debian系
shell> sudo apt update && sudo apt upgrade
···
# 使用Docker官方脚本安装
shell> sudo wget -qO- https://get.docker.com/ | bash
···
# 安装成功后将输出Docker版本信息
shell> docker --version
Docker version ···, build ···
```

### 2. 启动echoIP

启动容器并映射端口

```
# 映射容器服务到宿主机1601端口
shell> docker run -d --name echoip -p 1601:1601 dnomd343/echoip
# 查看容器状态
shell> docker ps -a
CONTAINER ID   IMAGE                    COMMAND           CREATED          STATUS        PORTS     NAMES
48d4b7a644e8   dnomd343/echoip          "sh init.sh"      12 seconds ago   Created                 echoip
```

如果服务器1601端口未配置防火墙，在浏览器输入 `http://服务器IP:1601/` 即可访问echoIP页面

```
# 测试容器是否正常工作
shell> curl 127.0.0.1:1601/8.8.8.8
···8.8.8.8的详细信息···
```

常用容器操作命令

```
# 删除容器
shell> docker rm -f echoip
···
# 列出全部镜像
shell> docker images
···
# 删除镜像
shell> docker rmi dnomd343/echoip
···
```

### 3. 配置反向代理

这里使用Nginx作为示例，其他Web服务原理类似。

```
# 进入Nginx配置目录
shell> cd /etc/nginx/conf.d
# 下载配置文件
shell> wget https://raw.githubusercontent.com/dnomd343/echoIP/master/conf/nginx/docker.conf -O ip.conf
# 修改配置文件中域名、证书、端口等信息
shell> vim ip.conf
```

如果你的网络无法正常访问Github，将下述内容写入配置文件亦可。

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
```

重启Nginx服务

```
shell> nginx -s reload
```

将配置的域名DNS解析到当前服务器，即可用该域名访问echoIP服务。