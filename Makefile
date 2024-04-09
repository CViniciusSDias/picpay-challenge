.PHONY: start test stop

start:
	docker compose up -d
migrate-test:
	docker compose run migrations
test: migrate-test
	docker compose exec app php bin/phpunit --testdox
stop:
	docker compose down
infection: migrate-test
	docker compose exec app php vendor/bin/infection