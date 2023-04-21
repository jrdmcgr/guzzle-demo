#!/bin/bash
set -euo pipefail

NAME="guzzle-demo"

function cleanup {
    echo "Cleaning up..."
    docker stop "$NAME" > /dev/null
    docker network rm "$NAME" > /dev/null
}

trap cleanup EXIT

docker network create "$NAME"

echo "Starting server..."
docker run \
    --rm \
    --detach \
    --name="$NAME" \
    --net "${NAME}" \
    --volume="${PWD}/server:/var/www/html" \
    php:8-apache

echo "Running benchmark..."
docker run \
    --rm \
    --name="${NAME}-test" \
    --net="$NAME" \
    --volume="$PWD:/app" \
    composer:2.5 sh -c "composer install && php guzzle_parallel.php"
