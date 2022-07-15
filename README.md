# Shorteria

A self-hosted url shortening service written in PHP without dependencies and focus on simplicity. Designed for shared and cloud hosting.

## Features

- Simple usage
- Tracking of visitor's user agent and timestamp of visit

## Usage

You have to authenticate with the token from the `project/config.php`.

### Web

After finishing the installation you can navigate to these routes:
- YOUR_DOMAIN.TLD/__new  `(create a new shortend url)`
- YOUR_DOMAIN.TLD/__details `(view usage counter)`

### REST-API

Example curl call to create a new shortend url

```shell
# will return the shortend url
curl --location --request POST 'https://your-domain.com/__new' \
--form 'token="your-secure-token"' \
--form 'url="https://url-to-be-shortend.com"' \
--form 'responseType="json"' \
--form 'comment="this comment is optional"'
```

Example curl call to edit shortend url

```shell
# will return a JSON data
curl --location --request POST 'https://your-domain.com/__edit?shortcode=sample-short-url-code' \
--form 'token="your-secure-token"' \
--form 'url="https://updated-url.com"' \
--form 'responseType="json"' \
--form 'comment="this comment is optional"'
```

## Requirements

- PHP 7.4 or higher
- MariaDB or MySQL
- A webserver (e.g. apache2)
- PHP extension: mysqli, json

## Installation

We soon support Docker in production environment - currently not.

### Clone this repository

```shell
git clone https://github.com/mr-software-gmbh/shorteria.git
```

### Config

Create a local environment config file:

```shell
cd project
cp config.php.dist config.php
```

Open the `config.php` file in an editor and set up the database connection.

### Database

```sql
-- may not be required on shared hosting | you should know if the database already exists or not
CREATE DATABASE shorteria CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
```

Manually execute all sql files which are stored in the `project/db` folder. The numerical order has to be respected.

### Webserver

Point the `DocumentRoot` of your webserver to the `project/public` folder of the above cloned folder.

**Apache2**: works out of the Box.

**Nginx**: you have to manually extend your server config:

```
server {
    location / {
        rewrite ^/(.*)$ /index.php?redirectTo=$1;
    }
}
```

### 3rd party software

- [picocss](https://github.com/picocss/pico/blob/master/LICENSE.md) (via CDN)


