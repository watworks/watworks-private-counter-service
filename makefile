dev-up:
	docker-compose up -d

dev-down:
	docker-compose down

deps:
	docker-compose run app composer install

test:
	docker-compose run app vendor/bin/phpunit

format:
	docker-compose run app vendor/bin/php-cs-fixer

build:
	echo TODO

publish: build
	echo TODO

.PHONY: dev-up dev-down deps test format build publish