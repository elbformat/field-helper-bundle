## How to run circle-ci tests locally

To run the steps from circle ci locally, you can use the same docker image
```bash
docker compose run php sh
composer install --dev
vendor/bin/phpunit --testsuite unit
vendor/bin/php-cs-fixer fix src
vendor/bin/php-cs-fixer fix tests
vendor/bin/psalm
```

To run integration tests, you need to spin up a database first. To ease this, you can use the docker-compose setup provided
```bash
docker compose up -d
docker compose run php sh
vendor/bin/phpunit --testsuite integration
```

When you need xdebug, you can activate it temporarily inside the running container
```bash
echo "zend_extension=xdebug.so" > /usr/local/etc/php/conf.d/xdebug.ini
export XDEBUG_CONFIG="client_host=172.17.0.1 idekey=PHPSTORM"
export XDEBUG_MODE=debug
```
