FROM alpine
LABEL version="1.2" \
      maintainer="dnomd343" \
      description="echo client IP details"
COPY . /var/www/echoIP
ADD ./conf/docker/init.sh /
RUN apk --update add --no-cache nginx curl nodejs php7 php7-fpm php7-json php7-iconv php7-sqlite3 php7-openssl php7-mbstring && \
    mkdir -p /run/nginx && touch /run/nginx/nginx.pid && \
    cp /var/www/echoIP/conf/docker/ip.conf /etc/nginx/echoip.conf && \
    cp -f /var/www/echoIP/conf/docker/nginx.conf /etc/nginx/nginx.conf && \
    cp /var/www/echoIP/conf/docker/init.sh / && \
    sed -i '$i\0\t0\t*\t*\t*\t/var/www/echoIP/backend/qqwryUpdate.sh' /var/spool/cron/crontabs/root
EXPOSE 1601
CMD ["sh","init.sh"]
