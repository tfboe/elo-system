FROM susa4ostec/bitbucket-pipelines-php7.1-mysql

# install xdebug (for code coverage)
RUN \
  apt-get update && \
  apt-get install -y php7.1-xdebug

# install mailcatcher
RUN \
  apt-get update && \
  apt-get install -y g++ make libsqlite3-dev ruby ruby-all-dev && \
  gem install mailcatcher

# configure php
RUN \
  sed -i -e 's/;sendmail_path =/sendmail_path = \/usr\/bin\/env catchmail -f catchmail@php.com/g' \
    /etc/php/7.1/cli/php.ini
