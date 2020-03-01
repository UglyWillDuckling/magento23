#!/usr/bin/env bash
php7.2 bin/magento setup:install \
--base-url=http://magento23:8090 \
--db-host=localhost \
--db-name=magento23 \
--db-user=root \
--db-password=mysql \
--backend-frontname=admin \
--admin-firstname=admin \
--admin-lastname=admin \
--admin-email=admin@admin.com \
--admin-user=admin \
--admin-password=admin123 \
--language=en_US \
--currency=USD \
--timezone=America/Chicago \
--use-rewrites=1