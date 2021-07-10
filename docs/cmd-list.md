## echoIP命令列表

echoIP使用User-agent判别是否为命令行环境，所有带有 `curl` 或 `wget` 的客户端查询均判别为命令行请求，该方式默认允许基于http的通讯方式，而不产生二次跳转。

使用 `/help` 指令可查看命令列表

```
shell> curl ip.343.re/help

echoIP - v1.3 (https://github.com/dnomd343/echoIP)

Format: http(s)://ip.343.re{Request_URI}

    / or /ip -> Show client IP.

    /info or /info/ -> Show detail of client IP.
    /{ip} or /info/{ip} -> Show detail of {ip}.

    /info/gbk -> Show detail of client IP (use GBK encoding).
    /{ip}/gbk or /info/{ip}/gbk -> Show detail of {ip} (use GBK encoding).

    /qr -> Show QR code of client IP (use special unicode characters).
    /qr/ -> Show QR code of client IP (use full characters).
    /qr/{xx} -> Show QR code of client IP (Use two custom characters).

    /help -> Show help message.
    /ua -> Show http user-agent of client.
    /version -> Show version of echoIP and IP database.

    /query?xxx=xxx&xxx=xxx
       |-> error=true: Show error request.
       |-> version=true: Show version of echoIP and IP database.
       |-> help=true: Show help message.
       |-> gbk=true: Use GBK encoding.
       |-> qr=true: Show QR code of client IP.
       |-> justip=true: Only query the client IP.
       |-> ip={ip}: Query of specified IP.
```

`/` 或 `/ip`：查询客户端IP地址。

```
shell> curl ip.343.re
47.242.30.65
shell> curl ip.343.re/ip
47.242.30.65
```

`/info` 或 `/info/`：查询客户端IP的详细信息。

```
shell> curl ip.343.re/info
IP: 47.242.30.65
AS: AS45102
City: Hong Kong
Region: Central and Western
Country: CN - China（中国）
Timezone: Asia/Shanghai
Location: 22.2783,114.1747
ISP: Alibaba (US) Technology Co., Ltd.
Scope: 47.242.0.0 - 47.244.255.255
Detail: 香港阿里云
```

`/{ip}` 或 `/info/{ip}`：查询指定IP的详细信息。

```
shell> curl ip.343.re/1.1.1.1
IP: 1.1.1.1
AS: AS13335
City: Miami
Region: Florida
Country: US - United States（美国）
Timezone: America/New_York
Location: 25.7867,-80.1800
ISP: Cloudflare, Inc.
Scope: 1.1.1.1/32
Detail: 美国APNIC&CloudFlare公共DNS服务器

shell> curl ip.343.re/info/8.8.8.8
IP: 8.8.8.8
AS: AS15169
City: Mountain View
Region: California
Country: US - United States（美国）
Timezone: America/Los_Angeles
Location: 37.4056,-122.0775
ISP: Google LLC
Scope: 8.8.8.8/32
Detail: 美国加利福尼亚州圣克拉拉县山景市谷歌公司DNS服务器
```

`/info/gbk`：查询客户端IP的详细信息，效果同 `/info` 或 `/info/`，使用GBK编码输出。

`/{ip}/gbk` 或 `/info/{ip}/gbk`：查询指定IP的详细信息，效果同 `/{ip}` 或 `/info/{ip}`，使用GBK编码输出。

GBK输出方式用于兼容Window10以下及部分早期版本的CMD，使echoIP返回中文信息不乱码。

`/qr`：使用特殊Unicode字符绘制客户端IP的URL二维码。

```
# 该方式在部分命令行下存在错位显示问题
shell> curl ip.343.re/qr
http://ip.343.re/?ip=47.242.30.65
█▀▀▀▀▀█ ▀▀    ▄█▀ ▄▀▄ █▀▀▀▀▀█
█ ███ █ █▄ █▀▀▀▀▄▄█▀█ █ ███ █
█ ▀▀▀ █ ▀█▀▀▄▀▄  ▄▄██ █ ▀▀▀ █
▀▀▀▀▀▀▀ ▀▄▀▄▀▄█ ▀▄▀ ▀ ▀▀▀▀▀▀▀
█▀█▀▄▄▀▄▀▄   ▀▀▄█▀ ▄▀▀▄ █▀▀ █
▄▀▀▀▀ ▀█   ▄▄▄██   ▄▀▄ █ ▄▀▀▄
▀▄▀▀ ▀▀█ ▀▄█ ▄   ██▄▀▀▄█ ▀▀▄▄
 █▄ ▄█▀ ▄█  █▄▀▄▄ █▀▀█▄▀█▀▀█▀
   █ ▀▀▄▀ ▄▀▄▄ ▄█ ▄▀██  ▀ ▄█
▀ ▄▀▀▀▀▀  █▀█  █▄█▀▄▀▀▄ ▄▀█
 ▀▀▀▀▀▀ █▀▄▀██▄  █▄▄█▀▀▀███▄▄
█▀▀▀▀▀█  ▄ ▀▀▀ ▄  ▀██ ▀ █▀ █▄
█ ███ █ ▄▀▄▄█ █▄ ██▄▀█▀██ ▀▀▄
█ ▀▀▀ █ █▄ █ ▀▄▄▀▀  █   ▄▄ ▄▀
▀▀▀▀▀▀▀ ▀▀  ▀ ▀ ▀   ▀▀ ▀ ▀ ▀
```

`/qr/`：使用满格的Unicode字符绘制客户端IP的URL二维码。

```
# 此方式显示错位几率较低，但是显示面积偏大
shell> curl ip.343.re/qr/
http://ip.343.re/?ip=47.242.30.65
██████████████  ████          ████    ██    ██████████████
██          ██              ████    ██  ██  ██          ██
██  ██████  ██  ██    ██████████    ██████  ██  ██████  ██
██  ██████  ██  ████  ██        ██████  ██  ██  ██████  ██
██  ██████  ██  ████████  ██          ████  ██  ██████  ██
██          ██    ██    ██  ██    ████████  ██          ██
██████████████  ██  ██  ██  ██  ██  ██  ██  ██████████████
                  ██  ██  ████    ██
████████    ██  ██        ████  ████    ████    ██████  ██
██  ██  ████  ██  ██          ████    ██    ██  ██      ██
  ████████  ████            ████        ██    ██    ████
██            ██      ██████████      ██  ██  ██  ██    ██
██  ████  ██████  ██  ██          ████  ████  ██  ████
  ██          ██    ████  ██      ██████    ████      ████
  ██      ████    ██    ██  ██      ████████  ████████████
  ████  ████    ████    ████  ████  ██    ████  ██    ██
      ██  ████  ██    ██        ██    ██████    ██    ██
      ██      ██    ██  ████  ████  ██  ████        ████
██    ██████████    ██████    ██  ████  ████      ████
    ██              ██  ██    ██████  ██    ██  ██  ██
  ████████████  ████  ██████      ██    ██████████████
                ██  ██  ██████    ████████      ██████████
██████████████        ██████        ██████  ██  ████  ██
██          ██    ██          ██      ████      ██    ████
██  ██████  ██    ██    ██  ██    ████  ██████████  ████
██  ██████  ██  ██  ██████  ████  ██████  ██  ████      ██
██  ██████  ██  ██    ██  ██    ████    ██              ██
██          ██  ████  ██    ████        ██      ████  ██
██████████████  ████    ██  ██  ██      ████  ██  ██  ██
```

`/qr/{xx}`：使用自定义字符绘制客户端IP的URL二维码。

```
# 此方式在等宽字体下不存在错位问题，但是显示效果较差
shell> curl ip.343.re/qr/@$
http://ip.343.re/?ip=47.242.30.65
@$@$@$@$@$@$@$  @$@$          @$@$    @$    @$@$@$@$@$@$@$
@$          @$              @$@$    @$  @$  @$          @$
@$  @$@$@$  @$  @$    @$@$@$@$@$    @$@$@$  @$  @$@$@$  @$
@$  @$@$@$  @$  @$@$  @$        @$@$@$  @$  @$  @$@$@$  @$
@$  @$@$@$  @$  @$@$@$@$  @$          @$@$  @$  @$@$@$  @$
@$          @$    @$    @$  @$    @$@$@$@$  @$          @$
@$@$@$@$@$@$@$  @$  @$  @$  @$  @$  @$  @$  @$@$@$@$@$@$@$
                  @$  @$  @$@$    @$
@$@$@$@$    @$  @$        @$@$  @$@$    @$@$    @$@$@$  @$
@$  @$  @$@$  @$  @$          @$@$    @$    @$  @$      @$
  @$@$@$@$  @$@$            @$@$        @$    @$    @$@$
@$            @$      @$@$@$@$@$      @$  @$  @$  @$    @$
@$  @$@$  @$@$@$  @$  @$          @$@$  @$@$  @$  @$@$
  @$          @$    @$@$  @$      @$@$@$    @$@$      @$@$
  @$      @$@$    @$    @$  @$      @$@$@$@$  @$@$@$@$@$@$
  @$@$  @$@$    @$@$    @$@$  @$@$  @$    @$@$  @$    @$
      @$  @$@$  @$    @$        @$    @$@$@$    @$    @$
      @$      @$    @$  @$@$  @$@$  @$  @$@$        @$@$
@$    @$@$@$@$@$    @$@$@$    @$  @$@$  @$@$      @$@$
    @$              @$  @$    @$@$@$  @$    @$  @$  @$
  @$@$@$@$@$@$  @$@$  @$@$@$      @$    @$@$@$@$@$@$@$
                @$  @$  @$@$@$    @$@$@$@$      @$@$@$@$@$
@$@$@$@$@$@$@$        @$@$@$        @$@$@$  @$  @$@$  @$
@$          @$    @$          @$      @$@$      @$    @$@$
@$  @$@$@$  @$    @$    @$  @$    @$@$  @$@$@$@$@$  @$@$
@$  @$@$@$  @$  @$  @$@$@$  @$@$  @$@$@$  @$  @$@$      @$
@$  @$@$@$  @$  @$    @$  @$    @$@$    @$              @$
@$          @$  @$@$  @$    @$@$        @$      @$@$  @$
@$@$@$@$@$@$@$  @$@$    @$  @$  @$      @$@$  @$  @$  @$
```

`/help`：显示帮助信息。

`/ua`：显示客户端User-agent，常在网页端使用。

```
shell> curl ip.343.re/ua
curl/7.29.0
```

`/version`：显示echoIP及IP数据库版本信息。

```
shell> curl ip.343.re/version
echoip -> v1.3
qqwry.dat -> 2021-07-07
ipip.net -> 2019-07-03
```

`/query?xxx=xxx&xxx=xxx`：原生查询接口。

+ `error=true`：返回错误信息/页面

+ `version=true`：显示echoIP及IP数据库版本信息

+ `help=true`：显示帮助信息

+ `gbk=true`：使用GBK编码

+ `qr=true`：显示客户端IP的二维码

+ `justip=true`：仅查询客户端IP地址

+ `ip={ip}`：查询的目标IP
