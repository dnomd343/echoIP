#!/bin/sh

ua="Mozilla/3.0 (compatible; Indy Library)"

cd `dirname $0`
mkdir -p temp
cd temp

if [ -n "$SOCKS5_CN" ]; then
  socks5=" --socks5 $SOCKS5_CN"
else
  socks5=""
fi

curl http://update.cz88.net/ip/copywrite.rar -o copywrite.rar$socks5 --user-agent '$ua'
curl http://update.cz88.net/ip/qqwry.rar -o qqwry.rar$socks5 --user-agent '$ua'

cat > unlock.php <<EOF 
<?php
\$copywrite = file_get_contents("copywrite.rar");
\$qqwry = file_get_contents("qqwry.rar");
\$key = unpack("V6", \$copywrite)[6];
for (\$i = 0; \$i < 0x200; \$i++) {
	\$key *= 0x805;
	\$key++;
	\$key = \$key & 0xFF;
	\$qqwry[\$i] = chr(ord(\$qqwry[\$i]) ^ \$key);
}
\$qqwry = gzuncompress(\$qqwry);
\$fp = fopen("qqwry.dat", "wb");
if (\$fp) {
	fwrite(\$fp, \$qqwry);
	fclose(\$fp);
}
?>
EOF

php unlock.php

file_size=`du qqwry.dat | awk '{print $1}'`
if [ $file_size = "0" ]; then
    echo "qqwry.dat update fail."
    cd .. && rm -rf temp/
    exit
fi

cd ..
cp -f temp/qqwry.dat qqwry.dat
rm -rf temp/
echo "qqwry.dat update complete."
