<?php

// 数据来源：纯真IP数据库 qqwry.dat
// 初始化类：new QQWry($fileName)
// 请求方式：getDetail($ip)
// 返回格式：
// {
//     "beginIP": IP段起始点
//     "endIP": IP段结束点
//     "dataA": 数据段1
//     "dataB": 数据段2
//     "country": 国家
//     "region": 行政区
//     "city": 城市
//     "domain": 所有者域名
//     "isp": ISP信息
// }
// 
// 请求版本：getVersion()
// 返回格式：YYYYMMDD

class QQWry {
    private $fp; // 文件指针
    private $firstRecord; // 第一条记录的偏移地址
    private $lastRecord; // 最后一条记录的偏移地址
    private $recordNum; // 总记录条数
    private $formatPort = '1602'; // 数据格式化分析接口

    public function __construct($fileName = 'qqwry.dat') { // 构造函数
        $this->fp = fopen($fileName, 'rb');
        $this->firstRecord = $this->read4byte();
        $this->lastRecord  = $this->read4byte();
        $this->recordNum = ($this->lastRecord - $this->firstRecord) / 7; // 每条索引长度为7字节
    }

    public function __destruct() { // 析构函数
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    private function read4byte() { // 读取4字节并转为long
        return unpack('Vlong', fread($this->fp, 4))['long'];
    }

    private function read3byte() { // 读取3字节并转为long
        return unpack('Vlong', fread($this->fp, 3) . chr(0))['long'];
    }

    private function readString() { // 读取字符串
        $str = '';
        $char = fread($this->fp, 1);
        while (ord($char) != 0) { // 读到二进制0结束
            $str .= $char;
            $char = fread($this->fp, 1);
        }
        return $str;
    }

    private function zipIP($ip) { // IP地址转为数字
        $ip_arr = explode('.', $ip);
        $tmp = (16777216 * intval($ip_arr[0])) + (65536 * intval($ip_arr[1])) + (256 * intval($ip_arr[2])) + intval($ip_arr[3]);
        return pack('N', intval($tmp)); // 32位无符号大端序长整型
    }

    private function unzipIP($ip) { // 数字转为IP地址
        return long2ip($ip);
    }

    public function getVersion() { // 获取当前数据库的版本
        fseek($this->fp, $this->lastRecord + 4);
        $tmp = $this->getRecord($this->read3byte())['B'];
        return substr($tmp, 0, 4) . substr($tmp, 7, 2) . substr($tmp, 12, 2);
    }

    public function getDetail($ip) { // 获取IP地址区段及所在位置
        if (!filter_var($ip, FILTER_VALIDATE_IP, FILTER_FLAG_IPV4)) { // 判断是否为IPv4地址
            return null;
        }
        
        fseek($this->fp, $this->searchRecord($ip)); // 跳转到对应IP记录的位置
        $detail['beginIP'] = $this->unzipIP($this->read4byte()); // 目标IP所在网段的起始IP
        $offset = $this->read3byte(); // 索引后3字节为对应记录的偏移量
        fseek($this->fp, $offset);
        $detail['endIP'] = $this->unzipIP($this->read4byte()); // 目标IP所在网段的结束IP

        $tmp = $this->getRecord($offset); // 获取记录的dataA与dataB
        $detail['dataA'] = $tmp['A'];
        $detail['dataB'] = $tmp['B'];

        if ($detail['beginIP'] == '255.255.255.0') { // 去除附加信息
            $detail['dataA'] = 'IANA';
            $detail['dataB'] = '保留地址';
        }
        if ($detail['dataA'] == ' CZ88.NET' || $detail['dataA'] == '纯真网络') {
            $detail['dataA'] = '';
        }
        if ($detail['dataB'] == ' CZ88.NET') {
            $detail['dataB'] = '';
        }
        $rawData = $this->formatData($detail['dataA'], $detail['dataB']);
        if ($rawData['dataA'] != '' && $rawData['dataB'] != '') {
            $detail['dataA'] = $rawData['dataA'];
            $detail['dataB'] = $rawData['dataB'];
        }
        $detail['country'] = $rawData['country'];
        $detail['region'] = $rawData['region'];
        $detail['city'] = $rawData['city'];
        $detail['domain'] = $rawData['domain'];
        $detail['isp'] = $rawData['isp'];
        return $detail;
    }

    private function searchRecord($ip) { // 根据IP地址获取索引的绝对偏移量
        $ip = $this->zipIP($ip); // 转为数字以比较大小
        $down = 0;
        $up = $this->recordNum;
        while ($down <= $up) { // 二分法查找
            $mid = floor(($down + $up) / 2); // 计算二分点
            fseek($this->fp, $this->firstRecord + $mid * 7);
            $beginip = strrev(fread($this->fp, 4)); // 获取二分区域的下边界
            if ($ip < $beginip) { // 目标IP在二分区域以下
                $up = $mid - 1; // 缩小搜索的上边界
            } else {
                fseek($this->fp, $this->read3byte());
                $endip = strrev(fread($this->fp, 4)); // 获取二分区域的上边界
                if ($ip > $endip) { // 目标IP在二分区域以上
                    $down = $mid + 1; // 缩小搜索的下边界
                } else { // 目标IP在二分区域内
                    return $this->firstRecord + $mid * 7; // 返回索引的偏移量
                }
            }
        }
        return $this->lastRecord; // 无法找到对应索引，返回最后一条记录的偏移量
    }

    private function getRecord($offset) { // 读取IP记录的数据
        fseek($this->fp, $offset + 4);
        $flag = ord(fread($this->fp, 1));
        if ($flag == 1) { // dataA与dataB均重定向
            $offset = $this->read3byte(); // 重定向偏移
            fseek($this->fp, $offset);
            if (ord(fread($this->fp, 1)) == 2) { // dataA再次重定向
                fseek($this->fp, $this->read3byte());
                $data['A'] = $this->readString();
                fseek($this->fp, $offset + 4);
                $data['B'] = $this->getDataB();
            } else { // dataA无重定向
                fseek($this->fp, -1, SEEK_CUR); // 文件指针回退1字节
                $data['A'] = $this->readString();
                $data['B'] = $this->getDataB();
            }
        } else if ($flag == 2) { // dataA重定向
            fseek($this->fp, $this->read3byte());
            $data['A'] = $this->readString();
            fseek($this->fp, $offset + 8); // IP占4字节, 重定向标志占1字节, dataA指针占3字节
            $data['B'] = $this->getDataB();
        } else { // dataA无重定向
            fseek($this->fp, -1, SEEK_CUR); // 文件指针回退1字节
            $data['A'] = $this->readString();
            $data['B'] = $this->getDataB();
        }
        $data['A'] = iconv("GBK", "UTF-8", $data['A']); // GBK -> UTF-8
        $data['B'] = iconv("GBK", "UTF-8", $data['B']);
        return $data;
    }

    private function getDataB() { // 从fp指定偏移获取dataB
        $flag = ord(fread($this->fp, 1));
        if ($flag == 0) { // dataB无信息
            return '';
        } else if ($flag == 1 || $flag == 2) { // dataB重定向
            fseek($this->fp, $this->read3byte());
            return $this->readString();
        } else { // dataB无重定向
            fseek($this->fp, -1, SEEK_CUR); // 文件指针回退1字节
            return $this->readString();
        }
    }

    private function formatData($dataA, $dataB) { // 从数据中提取国家、地区、城市、运营商等信息
        $str_json = file_get_contents('http://127.0.0.1:' . $this->formatPort . '/?dataA=' . urlencode($dataA) . '&dataB=' . urlencode($dataB));
        return json_decode($str_json, true); // 格式化为JSON
    }
}

?>
