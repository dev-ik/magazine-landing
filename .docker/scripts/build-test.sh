#!/usr/bin/env bash

#Environments
DOCKER_DIR=/builds/dev_ik/magazine-landing/

#Go to docker dir in project
if [ pwd != ${DOCKER_DIR} ]; then
cd ${DOCKER_DIR}
fi

mv docker-compose-test.yml docker-compose.yml

#Up docker containers for test environment
docker-compose build
docker-compose up -d

docker-compose ps

docker-compose exec -T magazine-php composer install --prefer-dist --optimize-autoloader --no-interaction