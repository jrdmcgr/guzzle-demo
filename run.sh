#!/bin/bash
set -euo pipefail

PORT="8080"
NAME="guzzle-demo"

function cleanup {
    echo "Cleaning up..."
    docker stop "$NAME" > /dev/null
    docker network rm "$NAME" > /dev/null
}

trap cleanup EXIT

docker network create "$NAME"
docker run \
    --rm \
    --detach \
    --name="$NAME" \
    --net "${NAME}" \
    --publish="${PORT}:80" \
    --volume="${PWD}/server:/var/www/html" \
    php:8-apache

# wait for it to start
echo "Starting server..."
while ! curl --silent http://localhost:"$PORT" > /dev/null
do sleep 1
done

echo "Running benchmark..."
docker run \
    --rm \
    --name="${NAME}-test" \
    --net="$NAME" \
    --volume="$PWD:/app" \
    php:8-cli \
    php /app/guzzle_parallel.php




