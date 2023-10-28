#!/bin/sh
mkdir -p -m 0750 /var/www/html/vault/Files
chown www-data:www-data /var/www/html/vault/Files
mkdir -p -m 0777 /var/www/html/vault/Icons
service cron start
exec apache2-foreground