NODES := $(shell docker network inspect php-redis-sample_redis_network | jq -r '.[0].Containers | map(select(.Name | test("php-redis-sample-redis.*"))) | .[].IPv4Address' | sed -r 's/\/[0-9]{2}/:6379 /' | sed -e ':a' -e 'N' -e '$!ba' -e 's/\n//g')

build:
	docker compose build

up:
	docker compose up -d

stop:
	docker compose stop

down:
	docker compose down

rebuild:
	docker compose build --no-cache

redis-cluster-init:
	docker compose exec redis bash -c "redis-cli --cluster create ${NODES} --cluster-replicas 1"

composer-install:
	docker compose exec php composer install

migrate:
	docker compose exec php ./vendor/bin/phinx migrate