<?php

class IpLocation {
    private $fp; // 文件指针
    private $firstip; // 第一条记录的偏移地址
    private $lastip; // 最后一条记录的偏移地址
    private $totalip; // 总记录条数

    public function __construct() { // 构造函数
        $this->fp = 0;
        if (($this->fp = fopen(__DIR__.'/qqwry.dat', 'rb')) !== false) {
            $this->firstip = $this->getlong();
            $this->lastip  = $this->getlong();
            $this->totalip = ($this->lastip - $this->firstip) / 7;
        }
    }

    public function __destruct() { // 析构函数
        if ($this->fp) {
            fclose($this->fp);
        }
        $this->fp = 0;
    }

    public function getDetail($ip) { // 获取IP地址区段及所在位置
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) { // 判断IP是否有效
            return null;
        }
        $location = $this->getLocation($ip);
        if (!$location) {
            return null;
        }
        $detail['cidr'] = $location['beginip'] . ' - ' . $location['endip'];
        $detail['addr'] = $location['country'] . $location['area'];
        return $detail;
    }

    private function getlong() { // 将读取的4字节转化为长整型数
        return unpack('Vlong', fread($this->fp, 4))['long'];
    }

    private function getlong3() { // 将读取的3字节转化为长整型数
        return unpack('Vlong', fread($this->fp, 3) . chr(0))['long'];
    }

    private function ip2long($ip) { // 将IP地址转为数字地址
        $ip_arr = explode('.', $ip);
        return (16777216 * intval($ip_arr[0])) + (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2])) + intval($ip_arr[3]);
    }

    private function packip($ip) { // 计算压缩后的IP地址
        return pack('N', intval($this->ip2long($ip)));
    }

    private function getstring($data = "") { // 读取字符串
        $char = fread($this->fp, 1);
        while (ord($char) > 0) { // 字符串读取到\0结束
            $data .= $char;
            $char = fread($this->fp, 1);
        }
        return $data;
    }

    private function getArea() { // 获取地区信息
        $flag = fread($this->fp, 1); // 标志字节
        if (ord($flag) == 0) { // 无区域信息
            return '';
        } else if (ord($flag) == 1 || ord($flag) == 2) { // 区域信息被重定向
            fseek($this->fp, $this->getlong3());
            return $this->getstring();
        } else { // 区域信息未被重定向
            return $this->getstring($flag);
        }
    }

    private function getLocation($ip) { // 根据IP地址返回地区信息
        $ip = $this->packip($ip);
        $l = 0; // 搜索下边界
        $u = $this->totalip; // 搜索上边界
        $findip = $this->lastip; // 若未找到则返回最后一条记录

        while ($l <= $u) { // 发起查找
            $i = floor(($l + $u) / 2); // 计算二分点
            fseek($this->fp, $this->firstip + $i * 7);
            $beginip = strrev(fread($this->fp, 4)); // 获取二分点所在区域的下边界
            if ($ip < $beginip) { // 目标IP小于二分区域的下边界
                $u = $i - 1; // 搜索的上边界缩小到二分点以下
            } else { // 目标IP大于或等于二分区域的下边界
                fseek($this->fp, $this->getlong3());
                $endip = strrev(fread($this->fp, 4)); // 获取二分区域的上边界
                if ($ip > $endip) { // 目标IP大于二分区域的上边界
                    $l = $i + 1; // 搜索的下边界缩小到二分点以上
                } else { // 目标IP在二分区域内
                    $findip = $this->firstip + $i * 7;
                    break;
                }
            }
        }
        fseek($this->fp, $findip);
        $location['beginip'] = long2ip($this->getlong()); // 目标IP所在区域的下边界
        $offset = $this->getlong3();
        fseek($this->fp, $offset);
        $location['endip'] = long2ip($this->getlong()); // 目标IP所在区域的上边界

        //获取目标IP的位置信息
        $byte = fread($this->fp, 1); // 标志字节
        switch (ord($byte)) {
            case 1: // 国家和区域信息均被重定向
                $countryOffset = $this->getlong3(); // 重定向地址
                fseek($this->fp, $countryOffset);
                $byte = fread($this->fp, 1); // 标志字节
                switch (ord($byte)) {
                    case 2: // 国家信息被重定向
                        fseek($this->fp, $this->getlong3());
                        $location['country'] = $this->getstring();
                        fseek($this->fp, $countryOffset + 4);
                        $location['area'] = $this->getArea();
                        break;
                    default: // 国家信息未被重定向
                        $location['country'] = $this->getstring($byte);
                        $location['area']    = $this->getArea();
                        break;
                }
                break;
            case 2: // 国家信息被重定向
                fseek($this->fp, $this->getlong3());
                $location['country'] = $this->getstring();
                fseek($this->fp, $offset + 8);
                $location['area'] = $this->getArea();
                break;
            default: // 国家信息未被重定向
                $location['country'] = $this->getstring($byte);
                $location['area'] = $this->getArea();
                break;
        }

        // 转为UTF-8编码
        $location['country'] = iconv("GBK", "UTF-8", $location['country']);
        $location['area'] = iconv("GBK", "UTF-8", $location['area']);

        // 去除附带信息
        if ($location['country'] == " CZ88.NET" || $location['country'] == "纯真网络") {
            $location['country'] = "Unknow";
        }
        if ($location['area'] == " CZ88.NET") {
            $location['area'] = "";
        }
        return $location;
    }
}
