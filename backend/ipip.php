<?php

// 数据来源：IPIP免费数据库 ipipfree.ipdb
// 初始化类：new IPDB($fileName)
// 请求方式：getDistrict($ip)
// 返回格式：
// {
//     '0': 国家
//     '1': 省、直辖市
//     '2': 市
// }
// 注：仅国内省市数据
// 
// 请求版本：getVersion()
// 返回格式：YYYYMMDD

class IPDB {
    private $fp; // 文件指针
    private $meta; // 元数据信息
    private $nodeCount;
    private $nodeOffset;

    public function __construct($fileName) { // 构造函数
        $this->fp = fopen($fileName, 'rb');
        $metaSize = unpack('N', fread($this->fp, 4))[1]; // 获取元数据长度
        $this->meta = json_decode(fread($this->fp, $metaSize), 1); // 读取元数据信息
        $this->nodeCount = $this->meta['node_count'];
        $this->nodeOffset = 4 + $metaSize;
    }

    public function __destruct() { // 析构函数
        if ($this->fp) {
            fclose($this->fp);
        }
    }

    public function getVersion() { // 获取版本信息
        return date("Ymd", $this->meta['build']);
    }

    public function getDistrict($ip) { // 获取地区信息
        $node = $this->getNode($ip);
        if ($node <= 0) {
            return NULL;
        }
        return explode("\t", $this->getData($node));
    }

    private function getNode($ip) { // 获取节点编号
        $node = 0;
        $binary = inet_pton($ip);
        for ($i = 0; $i < 96 && $node < $this->nodeCount; $i++) {
            if ($i >= 80) {
                $node = $this->readNode($node, 1);
            } else {
                $node = $this->readNode($node, 0);
            }
            if ($node > $this->nodeCount) {
                return 0;
            }
        }
        for ($i = 0; $i < 32; $i++) {
            if ($node >= $this->nodeCount) {
                break;
            }
            $node = $this->readNode($node, 1 & ((0xFF & ord($binary[$i >> 3])) >> 7 - ($i % 8)));
        }
        if ($node <= $this->nodeCount) {
            return NULL;
        }
        return $node;
    }

    private function readNode($node, $index) {
        return unpack('N', $this->read($node * 8 + $index * 4, 4))[1];
    }

    private function getData($node) { // 根据节点编号获取信息
        $offset = $node + $this->nodeCount * 7;
        $bytes = $this->read($offset, 2);
        $size = unpack('N', str_pad($bytes, 4, "\x00", STR_PAD_LEFT))[1];
        $offset += 2;
        return $this->read($offset, $size);
    }

    private function read($offset, $length) { // 从指定位置读取指定长度
        if ($length <= 0) {
            return NULL;
        }
        if (fseek($this->fp, $offset + $this->nodeOffset) === 0) {
            return fread($this->fp, $length);
        }
    }
}

?>