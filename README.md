# grammatista-server


Docker commands

```
docker network create grammatista-network
docker-compose up -d
docker-compose exec app bash
docker-compose down -v
```

Install app
```
composer install
php artisan migrate
php artisan key:generate
```

Code sniffer
```
vendor/bin/phpcs
vendor/bin/phpcbf
```