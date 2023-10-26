#!/bin/sh
mkdir -p -m 0750 /var/www/html/vault/Files
chown www-data:www-data /var/www/html/vault/Files
exec apache2-foreground