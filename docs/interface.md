## 开发常用接口

1. echoIP支持在URL中指定查询目标IP，格式形如 `https://ip.343.re/?ip=9.9.9.9`，访问时自动显示该IP地址的信息。

2. echoIP后端支持返回当前版本信息，接口位于 `/version` 下，若请求来自命令行，则返回可视化格式，否则返回JSON数据。

```
shell> curl ip.343.re/version
echoip -> v1.1
qqwry.dat -> 2021-04-21
ipip.net -> 2019-07-03

shell> curl https://ip.343.re/version --user-agent 'Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/90.0.4430.72 Safari/537.36 Edg/90.0.818.42'
{"echoip":"v1.1","qqwry":"20210421","ipip":"20190703"}
```

3. echoIP后端统一接口为 `/query`，可请求以下参数

```
error -> 请求出错
version -> 获取版本数据
cli -> 来自命令行下的请求
help -> 显示帮助信息
gbk -> 使用GBK编码
qr -> 显示二维码
justip -> 仅查询IP地址
ip -> 请求指定IP的数据
```

示例

```
shell> curl "ip.343.re/query?justip=true"
{"ip":"116.57.98.121"}

shell> curl "ip.343.re/query?justip=true&cli=true"
116.57.98.124

shell> curl "ip.343.re/query?cli=true&ip=7.7.7.7"
IP: 7.7.7.7
AS: AS8003
City: Atlantic City
Region: New Jersey
Country: US - United States（美国）
Timezone: America/New_York
Location: 39.3642,-74.4231
ISP: Global Resource Systems, LLC
Scope: 7.0.0.0/8
Detail: 美国俄亥俄州哥伦布市DoD网络信息中心
```

其他接口信息可见[命令列表](./cmd-list.md)
