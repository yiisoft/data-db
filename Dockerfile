FROM php:8.3-cli

# System packages

RUN apt-get update && apt-get install -y unzip libaio1

# Oracle Instant Client Downloads for Linux x86-64 (64-bit)

# RUN cd /tmp && curl -L https://download.oracle.com/otn_software/linux/instantclient/2350000/instantclient-basic-linux.x64-23.5.0.24.07.zip -O
# RUN cd /tmp && curl -L https://download.oracle.com/otn_software/linux/instantclient/2350000/instantclient-sdk-linux.x64-23.5.0.24.07.zip -O
# RUN cd /tmp && curl -L https://download.oracle.com/otn_software/linux/instantclient/2350000/instantclient-sqlplus-linux.x64-23.5.0.24.07.zip -O

# RUN unzip /tmp/instantclient-basic-linux.x64-23.5.0.24.07.zip -d /usr/local/
# RUN unzip -o /tmp/instantclient-sdk-linux.x64-23.5.0.24.07.zip -d /usr/local/
# RUN unzip -o /tmp/instantclient-sqlplus-linux.x64-23.5.0.24.07.zip -d /usr/local/

# RUN ln -s /usr/local/instantclient_23_5 /usr/local/instantclient
# Fixes error "libnnz19.so: cannot open shared object file: No such file or directory"
# RUN ln -s /usr/local/instantclient/lib* /usr/lib
# RUN ln -s /usr/local/instantclient/sqlplus /usr/bin/sqlplus

# RUN echo 'export LD_LIBRARY_PATH="/usr/local/instantclient"' >> /root/.bashrc
# RUN echo 'umask 002' >> /root/.bashrc

# PHP extensions

# RUN echo 'instantclient,/usr/local/instantclient' | pecl install oci8
# RUN echo "extension=oci8.so" > /usr/local/etc/php/conf.d/php-oci8.ini

# RUN echo 'instantclient,/usr/local/instantclient' | pecl install pdo_oci
# RUN echo "extension=pdo_oci.so" > /usr/local/etc/php/conf.d/php-pdo-oci.ini

RUN docker-php-ext-install pdo_mysql

# Composer

COPY --from=composer:2.8.1 /usr/bin/composer /usr/local/bin/composer

# Code

COPY . /code
WORKDIR /code

# PHP packages

RUN composer install
