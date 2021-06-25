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

# Installation

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
docker build -t docker/builder -f deployments/builder/Dockerfile .

docker run --rm -v $PWD:/var/app docker/builder composer install
```

5. Publish Configuration
```
php artisan vendor:publish --provider "NbsPhp\Core\Providers\CoreServiceProvider"
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

# run sample data seeder for development purpose
php artisan db:seed ---class=SampleSeeder
```

# Run Application

Using docker

```shell
   sh bin/create-core
```

# Deployments

### Set-up

1. Create **Deploy Tokens**
   > Go to Gitlab Project Settings > Repository > Deploy Tokens

1. Log-in to Container Registry
   ```shell
   echo ${CR_PASS} | docker login -u ${CR_USER} --password-stdin https://cr.nbs.co.id
   ```

### Deploy

1. Build Docker Image
    ```shell
    sh bin/build-images
    ```

1. Run Docker container
   ```shell
   sh bin/create-core

## What's next?

Import API Documentation

[![Run in Postman](https://run.pstmn.io/button.svg)](https://app.getpostman.com/run-collection/aa7c188c5dbd0aa6247b)

