start:
	docker compose up -d
test:
	docker compose run -e APP_ENV=test -e DATABASE_URL="sqlite:///%kernel.project_dir%/var/test_data.db" migrations
	docker compose exec -e APP_ENV=test -e DATABASE_URL="sqlite:///%kernel.project_dir%/var/test_data.db" app php bin/phpunit
stop:
	docker compose down
