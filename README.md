# palestra-server


Docker commands

```
docker network create palestra-network
docker-compose up -d
docker-compose exec app bash
docker-compose down -v
```

Install app
```
composer install
php artisan migrate
```

Code sniffer
```
vendor/bin/phpcs
vendor/bin/phpcbf
```