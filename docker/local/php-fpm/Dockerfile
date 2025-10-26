FROM debian:bookworm-slim

ARG USER_ID=1000
ARG GROUP_ID=1000

# Update system
RUN apt-get update && \
    apt-get -y dist-upgrade

# Install packages
RUN apt-get -y install htop \
    vim \
    lsb-release \
    ca-certificates \
    curl

# Add repo for php 8.3
RUN curl -sSLo /tmp/debsuryorg-archive-keyring.deb https://packages.sury.org/debsuryorg-archive-keyring.deb && \
    dpkg -i /tmp/debsuryorg-archive-keyring.deb && \
    sh -c 'echo "deb [signed-by=/usr/share/keyrings/deb.sury.org-php.gpg] https://packages.sury.org/php/ $(lsb_release -sc) main" > /etc/apt/sources.list.d/php.list' && \
    apt-get update

# Install packages
RUN apt-get install -y php8.3-cli \
    php8.3-bcmath \
    php8.3-bz2 \
    php8.3-common \
    php8.3-curl \
    php8.3-intl \
    php8.3-mbstring \
    php8.3-mcrypt \
    php8.3-mysql \
    php8.3-readline \
    php8.3-redis \
    php8.3-xml \
    php8.3-zip \
    php8.3-fpm

# Create log folder and files
RUN mkdir -p /var/log/php/cli && \
    touch /var/log/php/cli/error.log && \
    mkdir -p /var/log/php/fpm && \
    touch /var/log/php/fpm/error.log && \
    touch /var/log/php/fpm/fpm-error.log && \
    touch /var/log/php/fpm/www-access.log && \
    touch /var/log/php/fpm/www-slow.log && \
    chmod -R 0777 /var/log/php

# Forward logs to docker log collector
RUN ln -sf /dev/stdout /var/log/php/cli/error.log && \
    ln -sf /dev/stdout /var/log/php/fpm/error.log && \
    ln -sf /dev/stdout /var/log/php/fpm/fpm-error.log && \
    ln -sf /dev/stdout /var/log/php/fpm/www-access.log && \
    ln -sf /dev/stdout /var/log/php/fpm/www-slow.log

# Config files
COPY ./docker/local/php-fpm/config/etc/php/8.3/cli/php.ini /etc/php/8.3/cli
COPY ./docker/local/php-fpm/config/etc/php/8.3/fpm/php.ini /etc/php/8.3/fpm
COPY ./docker/local/php-fpm/config/etc/php/8.3/fpm/php-fpm.conf /etc/php/8.3/fpm
COPY ./docker/local/php-fpm/config/etc/php/8.3/fpm/pool.d/www.conf /etc/php/8.3/fpm/pool.d

# Create application user
RUN groupadd --system --gid $GROUP_ID application && \
    useradd --system --gid $GROUP_ID --create-home --home-dir /home/application --shell /bin/bash --uid $USER_ID application

# Switch user
USER application

# Create application dir
RUN mkdir -p /home/application/blog

# Set workdir
WORKDIR /home/application/blog

# Switch user
USER root

# Override stop signal to stop process gracefully
# https://github.com/php/php-src/blob/17baa87faddc2550def3ae7314236826bc1b1398/sapi/fpm/php-fpm.8.in#L163
STOPSIGNAL SIGQUIT

EXPOSE 9000
CMD ["/usr/sbin/php-fpm8.3"]
