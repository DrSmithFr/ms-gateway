log:
	symfony server:log

bind:
	symfony proxy:domain:attach ms-gateway

start:
	symfony proxy:start
	symfony server:start -d
	symfony run -d docker-compose up

kill:
	symfony server:stop

reset:
	symfony console doctrine:database:drop --force
	symfony console doctrine:database:create
	symfony console doctrine:migration:migrate -n
	symfony console doctrine:fixtures:load -n
