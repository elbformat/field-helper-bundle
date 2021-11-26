## How to run circle-ci tests locally

To run the steps from circle ci locally, you can use the same docker image
```bash
docker run -it -v $(pwd):/var/www ezsystems/php:7.4 bash
COMPOSER_MEMORY_LIMIT=-1 composer install --dev
phpdbg -qrr -d memory_limit=4G vendor/bin/phpunit
vendor/bin/php-cs-fixer fix src
vendor/bin/php-cs-fixer fix tests
vendor/bin/psalm
```

When you need xdebug, you can activate it temporarily inside the running container
```bash
docker run -it -v $(pwd):/var/www ezsystems/php:7.4 bash
mv /usr/local/etc/php/conf.d/xdebug.ini.disabled /usr/local/etc/php/conf.d/xdebug.ini
echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini
export XDEBUG_CONFIG="client_host=172.17.0.1 idekey=PHPSTORM"
export XDEBUG_MODE=debug
```
