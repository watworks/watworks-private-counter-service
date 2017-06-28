dev-up:
	docker-compose up -d

dev-down:
	docker-compose down

dev-init:
	make dev-up
	make deps-install

dev-rebuild: dev-down
	docker-compose up --build -d
	make deps-install

deps-install:
	docker-compose run --rm app composer install

deps-update:
	docker-compose run --rm app composer update

repl:
	docker-compose run --rm app vendor/bin/psysh src/app.php
	
test:
	docker-compose run --rm app vendor/bin/phpunit --colors=auto

format:
	docker-compose run --rm app vendor/bin/php-cs-fixer fix .

build:
	# force rebuilding deps to ensure only required deps are included
	# in the built image
	docker-compose run --rm app rm -rf vendor/*
	docker-compose run --rm app composer install --no-dev
	docker build -t watworks/watworks-private-counter-service .

publish:
	@if [ ! "$(TAG)" ]; then \
        echo "TAG was not specified"; \
        return 1; \
    fi
	docker-compose run --rm app rm -rf vendor/*
	docker-compose run --rm app composer install --no-dev
	docker build -t watworks/watworks-private-counter-service:$(TAG) .
	git tag $(TAG)
	git push origin --tags
	docker push watworks/watworks-private-counter-service:$(TAG)

.PHONY: dev-up dev-down dev-init deps-install deps-update repl test format build publish