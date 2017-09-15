FROM susa4ostec/bitbucket-pipelines-php7.1-mysql

# install mailcatcher
RUN \
  apt-get update && \
  apt-get install -y g++ make libsqlite3-dev ruby ruby-all-dev && \
  gem install mailcatcher

# configure php
RUN \
  sed -i -e 's/;sendmail_path =/sendmail_path = \/usr\/bin\/env catchmail -f catchmail@php.com/g' \
    /etc/php/7.1/cli/php.ini
