FROM d8vik/centos-php7.4:latest

ARG mode
ARG xdebug_host

COPY / /var/www/magazine.docker

COPY /.docker/php/entrypoint.sh /

RUN chmod +x /entrypoint.sh

RUN if [ "$mode" = "dev" ] ; then \
    echo "is Development install Xdebug" \
    && yum install --enablerepo=remi-php74 php-xdebug -y \
    && echo "zend_extension=xdebug.so" > /etc/php.d/15-xdebug.ini \
    && echo "xdebug.remote_enable=on" >> /etc/php.d/15-xdebug.ini \
    && echo "xdebug.remote_autostart=off" >> /etc/php.d/15-xdebug.ini \
    && echo "xdebug.remote_host=$xdebug_host" >> /etc/php.d/15-xdebug.ini \
    && echo "xdebug.remote_port=9000" >> /etc/php.d/15-xdebug.ini ;\
    fi

WORKDIR /var/www/magazine.docker

RUN composer install --prefer-dist --no-interaction --optimize-autoloader

ENTRYPOINT ["/entrypoint.sh"]

EXPOSE 9000
EXPOSE 9001
CMD php-fpm -F -R