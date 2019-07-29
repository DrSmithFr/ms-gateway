# Gateway

Simple gateway micro service.

## Installation

  - Install [Symfony Local Web Server](https://symfony.com/doc/current/setup/symfony_server.html)
  - Then run `make start`
  - Generate JWT keys

## Generate your JWT keys

YtSeries need public/private key to generate JWT tokens. 
Those tokens are used by API for authentication and reconnection mechanisms.

Execute the following commands:

    mkdir -p config/jwt
    
    openssl genrsa -out config/jwt/private.pem -aes256 4096
    openssl rsa -pubout -in config/jwt/private.pem -out config/jwt/public.pem
    openssl rsa -in config/jwt/private.pem -out config/jwt/private2.pem
    
    mv config/jwt/private2.pem config/jwt/private.pem
    chmod 777 config/jwt/*

## Coding style and Standards

- Respect [PSR-2 coding standards](documentation/PSR/PSR-2-coding-style-guide.md) for all php files.
- Respect [PHP mess detector](https://phpmd.org/rules/index.html).
- Respect [AirBnB standards guideline](https://github.com/airbnb/javascript) for javascript.
- Respect [PHPdoc standard](documentation/phpdoc.md).
- Respect every PSR for php files/classes.

Try to inspire you as mush as you can of [functional programing](https://www.youtube.com/watch?v=BMUiFMZr7vk&list=PL0zVEGEvSaeEd9hlmCXrk5yUyqUag-n84), it will make your code cleaner, reusable and easy to test. 
You will find equivalent in every programming language.

