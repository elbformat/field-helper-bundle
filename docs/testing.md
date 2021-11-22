## How to run circle-ci tests locally

To run the steps from circle ci locally, you can use the same docker image
```bash
docker run -it -v $(pwd):/var/www ezsystems/php:7.4 bash
composer install --dev
phpdbg -qrr vendor/bin/phpunit
vendor/bin/php-cs-fixer fix .
vendor/bin/psalm
```
