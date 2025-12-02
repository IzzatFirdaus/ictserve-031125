#!/usr/bin/env bash

HOST=${1:-127.0.0.1}
PORT=${2:-8080}
SCHEME=${3:-http}

echo "Starting Reverb on ${SCHEME}://${HOST}:${PORT}"

php artisan reverb:serve --host=${HOST} --port=${PORT} --scheme=${SCHEME}
