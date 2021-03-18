FROM debian
COPY . /root
ADD ./conf/docker/init.sh /
RUN mkdir -p /var/www/echoIP \
    && mv /root/* /var/www/echoIP/ \
    && apt update \
    && apt install -y nginx curl \
    && apt install -y php7.3 php7.3-fpm php7.3-sqlite3 \
    && apt clean \
    && cp /var/www/echoIP/conf/nginx/docker/ip.conf /etc/nginx/conf.d \
    && chmod +x /init.sh
CMD ["sh","init.sh"]
