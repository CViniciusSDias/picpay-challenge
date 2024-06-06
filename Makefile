.PHONY: start migrate-test test stop infection stan

start:
	docker run --rm -itv $(shell pwd):/app -w /app composer:2.7.2 composer install --ignore-platform-reqs
	docker compose up -d
migrate-test:
	docker compose run -eAPP_ENV=test -eDATABASE_URL="sqlite:///%kernel.project_dir%/var/test_data.db" migrations
test: migrate-test
	docker compose exec app php bin/phpunit --testdox
stop:
	docker compose down
infection: migrate-test
	docker compose exec app php vendor/bin/infection
stan:
	docker compose exec app php vendor/bin/phpstan analyse
