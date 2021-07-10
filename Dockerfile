FROM alpine as iconv
COPY ./conf/docker/iconv.sh /
RUN sh /iconv.sh

FROM alpine
LABEL maintainer="dnomd343"
COPY . /var/www/echoIP
COPY --from=iconv /tmp/iconv/ /usr/local/lib/
RUN apk --update add --no-cache nginx curl nodejs php7 php7-fpm php7-json php7-iconv php7-sqlite3 php7-openssl php7-mbstring && \
    rm /usr/lib/php7/modules/iconv.so && ln -s /usr/local/lib/iconv.so /usr/lib/php7/modules/ && \
    mv /usr/local/lib/libiconv.so /usr/local/lib/libiconv.so.2 && \
    mkdir -p /run/nginx && touch /run/nginx/nginx.pid && \
    cp /var/www/echoIP/conf/docker/init.sh / && \
    cp /var/www/echoIP/conf/docker/ip.conf /etc/nginx/echoip.conf && \
    cp -f /var/www/echoIP/conf/docker/nginx.conf /etc/nginx/nginx.conf && \
    cp /var/www/echoIP/conf/docker/init.sh / && \
    sed -i '$i\0\t0\t*\t*\t*\t/var/www/echoIP/backend/qqwryUpdate.sh' /var/spool/cron/crontabs/root
EXPOSE 1601
CMD ["sh","init.sh"]
