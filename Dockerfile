FROM alpine
COPY . /var/www/echoIP
ADD ./conf/docker/init.sh /
RUN apk --update add --no-cache nginx curl nodejs php7 php7-fpm php7-json php7-iconv php7-sqlite3 php7-openssl && \
    mkdir /run/nginx && touch /run/nginx/nginx.pid && \
    cp /var/www/echoIP/conf/nginx/docker.conf /etc/nginx/conf.d && \
    cp /var/www/echoIP/conf/docker/init.sh /
CMD ["sh","init.sh"]
