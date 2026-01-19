FROM alpine:3.19
LABEL maintainer='SMART - ULg <smart@uliege.be>'

# PHP 8.3 packages : 
# see https://pkgs.alpinelinux.org/package/edge/community/x86_64/php83 
# for a list of available extensions

RUN apk --update add --no-cache \
    curl \
    openssh \
    ca-certificates \
    git \
    lighttpd \
    php83 \
    php83-cgi \
    fcgi \
    php83-common \
    php83-bcmath \
    php83-ctype \
    php83-curl \
    php83-dom \
    php83-gd \
    php83-gettext \
    php83-ldap \
    php83-mbstring \
    php83-mysqli \
    php83-mysqlnd \
    # php83-pgsql \
    # php83-pdo_pgsql \
    php83-openssl \
    php83-pdo \
    php83-pdo_mysql \
    php83-phar \
    php83-posix \
    php83-session \
    php83-soap \
    php83-sodium \
    php83-tokenizer \
    php83-xml \
    php83-xmlreader \
    php83-xmlwriter \
    php83-zip \
    php83-simplexml \
    php83-fileinfo \
    && \
    rm -rf /var/cache/apk/*

RUN ln -s /usr/bin/php-cgi83 /usr/bin/php-cgi

ADD ./_setup/lighttpd.conf /etc/lighttpd/lighttpd.conf
RUN adduser www-data -G www-data -H -s /bin/false -D
RUN mkdir -p /run/lighttpd/ && \
    chown www-data. /run/lighttpd/

RUN ln -s /usr/bin/php83 /usr/bin/php
COPY ./_setup/php.ini /etc/php83/conf.d/custom.ini

COPY --from=composer/composer:latest-bin /composer /usr/bin/composer

EXPOSE 80
VOLUME /workspace

ENTRYPOINT ["/usr/sbin/lighttpd", "-D", "-f", "/etc/lighttpd/lighttpd.conf"]
# CMD php-fpm -D && lighttpd -D -f /etc/lighttpd/lighttpd.conf

# HEALTHCHECK --timeout=5s CMD curl --silent --fail http://localhost:80/SmartScoringService.php || exit 1
