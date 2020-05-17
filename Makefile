#!/usr/bin/make -f
# SHELL = /bin/sh

###########################################################
### About Makefile
###########################################################
###
### Установка команды make для Windows.
###   Из документации (https://gist.github.com/evanwill/0207876c3243bbb6863e65ec5dc3f058#make)
###    - Go to ezwinports (https://sourceforge.net/projects/ezwinports/files/make-4.2.1-without-guile-w32-bin.zip/download).
###    - Download make-4.1-2-without-guile-w32-bin.zip (get the version without guile).
###    - Extract zip.
###    - Copy the contents to your Git\mingw64\ merging the folders, but do NOT overwrite/replace any existing files.
###
###
### Не забудте указать путь директории в переменной PATH
###  Нажмите Win+R и введите SystemPropertiesAdvanced.exe, нажмите "переменные среды"
###  и добавте в переменную Path
###  значение "C:\Program Files\Git\mingw64\bin"
###  значение "C:\Program Files\Git\usr\bin"
###
###########################################################

APP_DIR=$(shell echo $$(cd . && pwd))
APP_SUB_DIR=app

up:
	cd $(APP_DIR) && $(MAKE) up_app
	cd $(APP_DIR) && $(MAKE) build_app
	@echo ---------------------------------------------
	@echo =============================================
	@echo == Done
	@echo =============================================

## =====================================================
## Поднятие контейнеров приложения
## =====================================================
up_app:
	cd $(APP_DIR) && docker-compose up -d

## =====================================================
## Сборка основного проекта: миграции, сиды и пр.
## =====================================================
build_app:
	cd $(APP_DIR) && cp $(APP_SUB_DIR)/.env.example $(APP_SUB_DIR)/.env
	cd $(APP_DIR) && docker-compose exec -T app composer install && \
	cd $(APP_DIR) && docker-compose exec -T app php artisan migrate && \
	cd $(APP_DIR) && docker-compose exec -T app php artisan key:generate & \
	wait

## =====================================================
## Сборка основного боевого проекта: миграции, сиды и пр.
## =====================================================
build_app_prod:
	cd $(APP_DIR) && cp $(APP_SUB_DIR)/.env.example $(APP_SUB_DIR)/.env
	cd $(APP_DIR) && docker-compose exec -T app composer install --optimize-autoloader --no-dev && \
	cd $(APP_DIR) && docker-compose exec -T app php artisan config:cache && \
	cd $(APP_DIR) && docker-compose exec -T app php artisan route:cache && \
	cd $(APP_DIR) && docker-compose exec -T app php artisan migrate && \
	cd $(APP_DIR) && docker-compose exec -T app php artisan key:generate & \
	wait

down:
	cd $(APP_DIR) && docker-compose down -v
