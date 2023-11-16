#!/bin/sh
mkdir -p -m 0750 /var/www/html/vault/Files
chown www-data:www-data /var/www/html/vault/Files
mkdir -p -m 0777 /var/www/html/vault/Icons
mkdir -p -m 0750 /var/www/html/password_maintenance/sql
chown www-data:www-data /var/www/html/password_maintenance/sql
service cron start
exec apache2-foreground