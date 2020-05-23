#### Для использования Traefik
- создаем сеть для связи Traefik и других контейнеров
- переходим в папку с Traefik
- запускаем Traefik
```
    docker network create proxy
    cd /path-to-project/.docker/traefik
    docker-compose up -d
```

####Запускаем проект
- Зайти в папку с проектом
- Переименовать `example.env` в `.env`
- Для разработки переименовать `docker-compose-dev.yml` в `docker-compose.yml`
- Запустить контейнер
```
    cd /path-to_project
    mv ./example.env ./.env
    mv ./docker-compose-dev.yml ./docker-compose.yml
    docker-compose up --build -d
```

#### Запуск тестов   
```
    docker-compose exec magazine-php ./vendor/bin/codecept build
    docker-compose exec magazine-php ./vendor/bin/codecept run   
```

Дашбоард `Traefik` будет доступен по адресу traefik.docker (при не обходимости внесите правки в `hosts`) как по http так и по https.
Проект будет доступен по адресу magazine.docker (при не обходимости внесите правки в `hosts`) как по http так и по https
Для корректной работы https на `localhost` рекомендую использовать [mkcert](https://github.com/FiloSottile/mkcert)

`docker-compose-prod.yml` - для выкатки на прод 

`docker-compose-test.yml` - для автоматического прохожения тестов в Gitlab CI  

