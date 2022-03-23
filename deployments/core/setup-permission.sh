#!/usr/bin/env bash

echo "  > Change ownership to www-data"
chown -R www-data:www-data .

echo "  > Change permission"
find storage -type f -exec chmod 644 {} \;
find storage -type d -exec chmod 755 {} \;

echo "  > Set all new files and dirs to inherit group id"
chmod ug+rwx storage
chmod g+s storage

echo "  > Done"
