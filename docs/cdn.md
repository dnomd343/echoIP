## CDN注意事项

### 转发原始客户端IP

若HTTP连接中存在 `HTTP_X_FORWARDED_FOR` 参数，echoIP优先选择其作为客户端IP，若不存在该参数则使用与服务器连接的IP地址作为客户端IP。

因此CDN服务器必须在转发时附带该参数，绝大多数情况下CDN服务器会默认在其中填入客户端真实IP，但部分CDN服务商可能会自定义一个额外的参数来存放该数据，这种情况需要修改WEB服务器配置，将该参数内容拷贝到`HTTP_X_FORWARDED_FOR` 中。

若以上配置出错，可能会导致返回IP地址为CDN服务器IP而非客户端IP，在部署并启用CDN服务后务必测试该问题。

### 关闭服务器Gzip压缩

由于CDN向服务器请求的动态数据较短，压缩效果不大，而静态资源无需持续回源，开启压缩反而浪费服务器资源，因此无需在服务器上配置GZIP压缩，配置示例如下。

```
server {
    listen 80;
    listen [::]:80;
    server_name ip.dnomd343.top;
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
    server_name ip.dnomd343.top;
    ssl_certificate /etc/ssl/certs/dnomd343.top/fullchain.pem;
    ssl_certificate_key /etc/ssl/certs/dnomd343.top/privkey.pem;
    location / {
        proxy_set_header Host $http_host;
        proxy_set_header X-Real-IP $remote_addr;
        proxy_pass http://127.0.0.1:1601;
    }
}
```

但CDN服务对客户端的压缩功能建议启用，如Brotli压缩或Gzip压缩，都支持的情况下优先选择前者。

### CDN回源配置

CDN源站指向部署了echoIP的服务器，回源HOST使用当前echoIP域名，其DNS解析一般通过CNAME或NS方式指向CDN服务商提供的地址。

CDN服务建议开启HTTPS服务，如果支持 `HTTP/2`、 `TLS1.3`、`OCSP Stapling` 等特性建议打开，但务必关闭强制HTTPS模式或HSTS功能，否则命令行请求需带上 `https` 前缀或在curl命令中使用 `-vL` 参数。

所有协议配置均为跟随客户端协议回源，若服务器上部署了多个不同主域名的服务，切记开启SNI回源功能。

### 关闭HTML优化功能

部分CDN服务商提供了文件优化功能，将网页文件进行精简，删去其中空白内容。此处务必关闭HTML优化功能，否则命令行请求可能出现格式错乱，但CSS与JS等的优化可正常开启。

### CDN静态文件

echoIP的静态文件包括 `/assets/` 目录下所有文件以及 `/error` 页面，其余路径均不建议配置为静态数据。

### IPv6地址

若CDN支持IPv6服务，建议打开该功能，否则无法查询客户端IPv6地址。
