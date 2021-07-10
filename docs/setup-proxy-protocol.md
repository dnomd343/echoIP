## Proxy Protocol支持

在某些情况下，Nginx无法直接监听80与443端口，而是通过前置服务转发到指定端口，这种情况下配置文件需要稍加改动，同时前置服务器应开启 `Proxy Protocol` 支持。

若未配置 `Proxy Protocol` 协议，则Nginx无法得知客户端IP地址，此时所有查询结果均为前置服务器IP地址。

Nginx配置文件改动如下

```
# TCP/81端口接收携带Proxy Protocol的http流量
server {
    listen 81 proxy_protocol;
    listen [::]:81 proxy_protocol;
    server_name ip.343.re; # 改为自己的域名
    location / {
        if ($http_user_agent !~* (curl|wget)) {
            return 301 https://$server_name$request_uri;
        }
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $proxy_protocol_addr;
        proxy_pass http://127.0.0.1:1601;
    }
}

# TCP/444端口接收携带Proxy Protocol的https流量
server {
    listen 444 ssl http2 proxy_protocol;
    listen [::]:444 ssl http2 proxy_protocol;
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
        proxy_set_header X-Real-IP $proxy_protocol_addr;
        proxy_pass http://127.0.0.1:1601;
    }
}
```
