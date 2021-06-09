#!/usr/bin/env bash

echo "  > Change ownership to www-data"
chgrp -R www-data .

echo "  > Change permission"
find storage bootstrap/cache -type f -exec chmod 644 {} \;
find storage bootstrap/cache -type d -exec chmod 755 {} \;

echo "  > Set all new files and dirs to inherit group id"
chmod ug+rwx storage bootstrap/cache
chmod g+s storage bootstrap/cache

echo "  > Done"
