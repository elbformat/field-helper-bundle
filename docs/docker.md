## Docker php image

The docker php image is used by circleci. Formerly it was just `ezsystems/php:7.4`, but as the removed ssh support, we need to build a new one.
It is hosted in docker hub, but with a free account, so updates must be built and pushed manually
```bash
docker build docker/ --pull -f docker/Dockerfile.php -t hgiesenow/php:7.4
docker push hgiesenow/php:7.4
```
