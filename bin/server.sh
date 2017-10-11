#!/usr/bin/env bash

php -S localhost:3000 "$(dirname "$(dirname "$(readlink -m "$(pwd)/$0")")")/web/app.php"
