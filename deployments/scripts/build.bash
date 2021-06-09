#!/usr/bin/env bash

docker build -t docker/builder -f deployments/builder/Dockerfile .
docker run --rm -v $PWD:/var/app docker/builder composer install
