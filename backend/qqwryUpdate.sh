#!/bin/sh

cd `dirname $0`
mkdir -p temp
cd temp
wget http://update.cz88.net/ip/copywrite.rar
wget http://update.cz88.net/ip/qqwry.rar

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
cd ..
cp -f temp/qqwry.dat qqwry.dat
rm -rf temp/
echo "qqwry.dat update complete."