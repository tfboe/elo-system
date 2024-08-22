FROM ubuntu:20.04

# https://github.com/cmiles74/docker-vscode
# https://hub.docker.com/r/joengenduvel/docker-vscode/dockerfile

ENV DISPLAY=10.0.75.1:0.0
ARG DEBIAN_FRONTEND=noninteractive

##
## ubuntu stuff
##

RUN apt-get update \
 && apt-get install -y curl apt-transport-https libgtk2.0-0 libxss1 libasound2 xauth x11-apps dbus git gpg libx11-xcb-dev wget

RUN mkdir /var/run/dbus

##
## /ubuntu stuff
##

##
## vscodium stuff
##

RUN wget -qO - https://gitlab.com/paulcarroty/vscodium-deb-rpm-repo/-/raw/master/pub.gpg | gpg --dearmor | dd of=/etc/apt/trusted.gpg.d/vscodium.gpg \
  && echo 'deb https://paulcarroty.gitlab.io/vscodium-deb-rpm-repo/debs/ vscodium main' | tee --append /etc/apt/sources.list.d/vscodium.list \
  && apt-get update && apt-get install -y codium libxshmfence-dev
   
# not sure what this does!
#RUN cp /usr/lib/x86_64-linux-gnu/libxcb.so.1 /usr/share/code/ \
#  && cp /usr/lib/x86_64-linux-gnu/libxcb.so.1.1.0 /usr/share/code/ \
#  && sed -i 's/BIG-REQUESTS/_IG-REQUESTS/' /usr/share/code/libxcb.so.1 \
#  && sed -i 's/BIG-REQUESTS/_IG-REQUESTS/' /usr/share/code/libxcb.so.1.1.0

# change user so vscodium does not run as root
ENV USER=dev
ENV GROUP=developers
RUN groupadd $GROUP
RUN useradd -m -G $GROUP $USER -s /bin/bash
USER $USER

RUN which codium

# vscodium-extensions
RUN /usr/bin/codium --install-extension bmewburn.vscode-intelephense-client
RUN /usr/bin/codium --install-extension felixfbecker.php-debug
RUN /usr/bin/codium --install-extension mhutchie.git-graph

RUN /usr/bin/codium --list-extensions

USER root

##
## /vscode stuff
##


##
## php stuff
##

# lftp is for uploading php projects to a server via ftp using scripts
# nodejs for installing apidoc
RUN apt-get install software-properties-common -y
RUN add-apt-repository ppa:ondrej/php
RUN apt-get update \
  && apt-get install -y php8.2 php8.2-mbstring php8.2-dom php8.2-xml php8.2-xdebug mysql-client php8.2-mysql php8.2-curl php8.2-zip unzip lftp nodejs npm

# configure xdebug
RUN echo "xdebug.mode = debug\nxdebug.start_with_request = yes" >> /etc/php/8.2/mods-available/xdebug.ini


# php modules
RUN phpenmod mbstring \
  && phpenmod dom \
  && phpenmod xml \
  && phpenmod xdebug \
  && phpenmod pdo_mysql \
  && phpenmod curl \
  && phpenmod zip

# install composer
RUN curl -sS https://getcomposer.org/installer | php -- --install-dir=/usr/local/bin --filename=composer

# apidoc cli
RUN npm install apidoc -g

##
## /php stuff
##

RUN mkdir /userdata && chown $USER:$GROUP /userdata

WORKDIR /workspace

COPY entrypoint.bash /usr/bin/entrypoint.bash
ENTRYPOINT /bin/bash /usr/bin/entrypoint.bash
