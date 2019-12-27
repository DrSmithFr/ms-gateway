build:
	symfony composer install
	symfony console cache:clear

reload: stop start

start:
	symfony proxy:start
	symfony server:start -d
	symfony run -d docker-compose -f docker-compose.yaml -f docker-compose.override.yaml up -d

stop:
	symfony server:stop

log:
	symfony server:log

bind:
	symfony proxy:domain:attach ms-gateway

database:
	symfony console doctrine:database:drop --force
	symfony console doctrine:database:create
	symfony console doctrine:migration:migrate -n
	symfony console doctrine:fixtures:load -n
