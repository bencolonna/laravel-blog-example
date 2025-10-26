FROM debian:bookworm-slim

ARG USER_ID=1000
ARG GROUP_ID=1000

# Update system
RUN apt-get update && \
    apt-get -y dist-upgrade

# Install packages
RUN apt-get -y install htop \
    vim \
    nginx

# Create log folder and files
RUN mkdir -p /var/log/nginx && \
    touch /var/log/nginx/access.log && \
    touch /var/log/nginx/error.log && \
    chmod -R 0777 /var/log/nginx

# Forward logs to docker log collector
RUN ln -sf /dev/stdout /var/log/nginx/access.log && \
    ln -sf /dev/stdout /var/log/nginx/error.log

# Config files
RUN rm /etc/nginx/sites-enabled/default
COPY ./docker/local/nginx/config/etc/nginx/nginx.conf /etc/nginx
COPY ./docker/local/nginx/config/etc/nginx/conf.d/default.conf /etc/nginx/conf.d

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

STOPSIGNAL SIGQUIT

EXPOSE 80
CMD ["nginx", "-g", "daemon off;"]
