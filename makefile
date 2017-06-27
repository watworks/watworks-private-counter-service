dev-up:
	docker-compose up -d

dev-down:
	docker-compose down

deps-install:
	docker-compose run app composer install

deps-update:
	docker-compose run app composer update

repl:
	docker-compose run app vendor/bin/psysh src/app.php
	
test:
	docker-compose run app vendor/bin/phpunit

format:
	docker-compose run app vendor/bin/php-cs-fixer fix .

build:
	echo TODO

publish: build
	echo TODO

.PHONY: dev-up dev-down deps-install deps-update repl test format build publish