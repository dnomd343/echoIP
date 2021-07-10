## IP上游查询接口

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

离线数据库，在[官网](https://www.ipip.net/product/ip.html)登录后即可下载，国内可精确到市，格式为ipdb，数据不定期更新。

数据库文件位于 `backend/ipipfree.ipdb`， 查询代码位于 `backend/ipip.php`

### 纯真IP库

离线数据库，获取方式及解码原理可以参考[这里](https://blog.dnomd343.top/qqwry.dat-analyse/)，国内定位精度较高，数据每5天更新一次。

数据库文件位于 `backend/qqwry.dat`，数据库更新脚本位于 `backend/qqwryUpdate.sh`，查询代码位于 `backend/qqwry.php`，数据解析服务位于 `backend/qqwryFormat/*`

Docker部署方式中，容器内已经预留了 `qqwry.dat` 的自动升级功能，每天00:00时会运行脚本拉取数据库更新。对于常规部署方式，可以配置 `crontab` 自动执行更新脚本，示例如下

```
# 打开crontab任务列表
shell> crontab -e
···
# 添加如下一行，表示每天00:00时自动运行指定脚本
0   0   *   *   *   /var/www/echoIP/backend/qqwryUpdate.sh
```
