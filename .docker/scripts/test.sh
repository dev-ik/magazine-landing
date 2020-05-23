#!/usr/bin/env bash

docker-compose exec -T magazine-php cp ./example.env ./.env

docker-compose exec -T magazine-php php migration migrate -e testing

docker-compose exec -T magazine-php ./vendor/bin/codecept build

docker-compose exec -T magazine-php ./vendor/bin/codecept run