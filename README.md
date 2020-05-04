# grammatista-server

Make commands
```
make up
make down
```

Docker commands
```
docker network create grammatista-network
docker-compose up -d
docker-compose exec app bash
docker-compose down -v
```

Install the app (runs into the app)
```
composer install
php artisan migrate
php artisan key:generate
```

Code sniffer (runs into the app)
```
vendor/bin/phpcs
vendor/bin/phpcbf
```
