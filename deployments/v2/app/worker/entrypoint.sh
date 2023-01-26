#!/usr/bin/env bash

echo '  > '
if [ ! -d "/etc/supervisor/conf.d" ]; then
    echo '  > WARN: /etc/supervisor/conf.d not found'
    echo '  > '
    echo '  > Creating a supervisor conf.d directory...'
    echo '  > mkdir /etc/supervisor/conf.d'
    mkdir /etc/supervisor/conf.d
    echo '  > mkdir: "/etc/supervisor/conf.d": Is a directory'
fi

echo '  > '
echo '  > Symlinking supervisor configuration...'
ln -sf /etc/supervisor/conf.d-available/app.conf /etc/supervisor/conf.d/app.conf

echo '  > '
echo '  > Execute supervisord...'
exec supervisord -c /etc/supervisor/supervisord.conf
