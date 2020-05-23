########################################################################################################################
##
## PHP
##
########################################################################################################################
FROM php:7.3-fpm AS php

## DEFAULT EXTENSIONS
RUN apt-get update && apt-get install --no-install-recommends -y \
        libzip-dev \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libxpm-dev \
        libvpx-dev \
    && docker-php-ext-install bcmath \
    && docker-php-ext-install opcache \
    && docker-php-ext-install pdo_mysql \
    && docker-php-ext-install mysqli \
    && docker-php-ext-install zip \
    && docker-php-ext-install exif \
    && pecl install xdebug-2.7.2  \
    && rm -rf /var/lib/apt/lists/*

RUN docker-php-ext-configure gd \
        --with-freetype-dir=/usr/lib/x86_64-linux-gnu/ \
        --with-jpeg-dir=/usr/lib/x86_64-linux-gnu/ \
        --with-xpm-dir=/usr/lib/x86_64-linux-gnu/ \
        --with-vpx-dir=/usr/lib/x86_64-linux-gnu/ \
    && docker-php-ext-install gd

# Clear cache
RUN apt-get clean && rm -rf /var/lib/apt/lists/*

#RUN sed -ri 's/^www-data:x:82:82:/www-data:x:1000:50:/' /etc/passwd


## Install extensions
##RUN docker-php-ext-install pdo_mysql mbstring zip exif pcntl
##RUN docker-php-ext-configure gd --with-gd --with-freetype-dir=/usr/include/ --with-jpeg-dir=/usr/include/ --with-png-dir=/usr/include/
##RUN docker-php-ext-install gd
#
## Install composer
#RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer
#
## Add user for laravel application
RUN groupadd -g 1000 www
RUN useradd -u 1000 -ms /bin/bash -g www www
#
## Copy existing application directory contents
#COPY . /var/www

## Copy existing application directory permissions
#COPY --chown=www:www . /var/www
#
#COPY ./data /var/www/data
#RUN find /var/www/data -type d -exec chmod -v 0770 {} + \
#    && find /var/www/data -type f -exec rm -v {} + \
#    && chown -Rv www:www /var/www/data
#VOLUME ["/var/www/data"]
#
## Change current user to www
USER www

CMD ["php-fpm"]
