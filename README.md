# Micro Token

A micro-service to convert a credit card number to a secure token.

This project is a demo, do not use it in production.

# How to install

After cloning the project, get all dependencies:

    $ cp config/parameters.json.dist config/parameters.json
    $ composer install

# Test the application

A built-in server is provided, run:

    $ bin/server.sh

The server is now listening to `http://localhost:3000`.

Behat, PhpUnit and PhpSpec are used for functional, integration and unit testing.

    $ bin/behat
    $ bin/phpunit
    $ bin/phpspec run