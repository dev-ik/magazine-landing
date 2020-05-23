#!/bin/sh
set -e

cd /var/www/magazine.docker

php migration migrate

exec "$@"