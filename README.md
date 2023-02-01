# Lumen Starter

Lumen starter project for REST API

# Prerequisites

## Hardware
1. **CPU**: 1 Core
2. **RAM**: 1 GB 
3. **HDD**: 25 GB

## Software

1. **Composer**: "2.0"
1. **PHP**: "^7.4"
1. **Docker**: "^17"
1. **Docker Compose**: "^1.21"
1. **Git**

## Installation

1. Copy file `.env.example` to `.env`

```
cp .env.example .env
```

2. Fill form environment on file `.env` on `root` directory

3. Generate Key

```
php artisan key:generate
```

4. Build

Run on `root` directory

```
composer install
```

or using docker

```
docker build -t sanf/builder -f deployments/builder/Dockerfile .

docker run --rm -v $PWD:/var/www sanf/builder composer install
```

## Configuration

| Key                       | Required           | Values                                                    |
| ------------------------- | ------------------ | --------------------------------------------------------- |
| `APP_NAME`                | **✓** | `"Pastech API"`                                           |
| `APP_ENV`                 | **✓** | `local`,`development`, `production` (Default), `testing`  |
| `APP_KEY`                 | **✓** |                                                           |
| `APP_DEBUG`               |                    | `true`, `false` (Default)                                 |
| `APP_URL`                 | **✓** | `https://pastech-api.test`                                |
| `LOG_CHANNEL`             | **✓** |                                                           |
| `LOG_SLACK_WEBHOOK_URL`   |                    |                                                           |
| `DB_CONNECTION`           | **✓** | `pgsql`                                                   |
| `DB_HOST`                 | **✓** | `127.0.0.1`                                               |
| `DB_PORT`                 | **✓** | `5432`                                                    |
| `DB_USERNAME`             | **✓** | -                                                         |
| `DB_PASSWORD`             | **✓** | -                                                         |
| `DB_DATABASE`             | **✓** | -                                                         |
| `CACHE_DRIVER`            | **✓** | `file`,`redis`                                            |
| `QUEUE_CONNECTION`        | **✓** | `sync`, `database`, `redis`                               |
| `REDIS_HOST`              |                    |                                                           |
| `REDIS_PASSWORD`          |                    |                                                           |
| `REDIS_PORT`              |                    |                                                           |
| `MAIL_DRIVER`             | **✓** | `log`,`smtp`, `mailgun`                                   |
| `MAIL_HOST`               |                    |                                                           |
| `MAIL_PORT`               |                    |                                                           |
| `MAIL_ENCRYPTION`         |                    | `tls`(Default)                                            |
| `MAIL_FROM_ADDRESS`       | **✓** |                                                           |
| `MAIL_FROM_NAME`          |                    |                                                           |
| `FILESYSTEM_DRIVER`       | **✓** | `local`, `minio`                                          |
| `MINIO_ACCESS_KEY_ID`     |                    |                                                           |
| `MINIO_SECRET_ACCESS_KEY` |                    |                                                           |
| `MINIO_BUCKET`            |                    |                                                           |
| `MINIO_ENDPOINT`          |                    |                                                           |
| `JWT_SECRET`              | **✓** | `${APP_KEY}`                                              |
| `JWT_TTL`                 | **✓** |                                                           |
| `JWT_REFRESH_TTL`         | **✓** |                                                           |
| `JWT_ISSUER`              | **✓** | `${APP_NAME}`                                             |
| `BASIC_CLIENT_ID`         | **✓** |                                                           |
| `BASIC_CLIENT_SECRET`     | **✓** |                                                           |

# Database Administration

## Migration

```
php artisan migrate
```

## Seeder

```
composer dump-autoload

# OPTIONAL run sample data seeder for development purpose
php artisan db:seed --class=SampleSeeder

$ run master location table seeder
php -d=memory_limit=-1 artisan db:seed --class=LocationSeeder
```

## Run Application

Example Using docker

```shell
# api
docker build -f deployments/api/Dockerfile -t sanf/api .
sudo docker run -d --name sanf-api -v "$(pwd)":/var/www -p 80:8080 --restart unless-stopped sanf/api

# worker
docker build -f deployments/worker/Dockerfile -t sanf/worker .
docker run -d --name sanf-worker -v "$(pwd)":/var/www  --restart unless-stopped sanf/worker

```

Example Using docker compose

```shell
# all infra stack in container
docker-compose up -d

# or api only
docker-compose up -d api

# or worker only
docker-compose up -d worker
```

## Deployment

### Directory Permission
```
# go to project root directory
# set ownership to nobody:nogroup
 chown -R nobody:nogroup .
```

