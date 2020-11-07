FROM php:7.3.11-apache

# Extensões do PHP
RUN docker-php-ext-install mysqli

# Habilitando o xdebug
RUN pecl install xdebug
RUN docker-php-ext-enable xdebug

# Instalando o PDO MySql
RUN docker-php-ext-install pdo_mysql

# Criar os diretórios específicos para a aplicação do site
RUN mkdir /var/www/html/reservas.dlx.com.br

# Copiar o VirtualHost do site
COPY apache/reservas.dlx.com.br.conf /etc/apache2/sites-available/

# Copiar arquivos de configuração do PHP
COPY php/* /usr/local/etc/php/conf.d

# Ativar o php.ini
RUN cp /usr/local/etc/php/php.ini-development /usr/local/etc/php/php.ini

# Habilitar módulos do apache
RUN a2enmod rewrite

# Habilitar o site
RUN a2dissite 000-default
RUN a2ensite reservas.dlx.com.br

EXPOSE 8082
