#!/usr/bin/env bash

#Environments
DOCKER_DIR=/builds/dev_ik/magazine-landing/

#Go to docker dir in project
if [ pwd != ${DOCKER_DIR} ]; then
cd ${DOCKER_DIR}
fi

docker-compose down -v