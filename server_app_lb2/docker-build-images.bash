#!/bin/bash

# Sets script's behavior: exit on first error
set -e

docker-compose down
docker build -t php_img -f .docker/php/Dockerfile .
docker build -t nginx_img -f .docker/nginx/Dockerfile ./
docker-compose up --build

# Run migrations
printf 'waiting for docker services to up'
sleep 2s
docker-compose exec php php artisan migrate --seed
