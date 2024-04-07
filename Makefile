.PHONY: start test stop

start:
	docker compose up -d
migrate-test:
	docker compose run -e APP_ENV=test -e DATABASE_URL="sqlite:///%kernel.project_dir%/var/test_data.db" migrations
test: migrate-test
	docker compose exec -e APP_ENV=test -e DATABASE_URL="sqlite:///%kernel.project_dir%/var/test_data.db" app php bin/phpunit --testdox
stop:
	docker compose down
infection: migrate-test
	docker compose exec -e APP_ENV=test -e DATABASE_URL="sqlite:///%kernel.project_dir%/var/test_data.db" app php vendor/bin/infection