run:
	php "src/bootstrap.php"

# Arranca el server con xdebug en modo step-debugging (para dape/breakpoints).
# El `run` normal queda intacto y rapido; esto es opt-in para depurar.
run-debug:
	XDEBUG_MODE=debug XDEBUG_START_WITH_REQUEST=yes php "src/bootstrap.php"

watch:
	./src/ddd/Infrastructure/scripts/watch.sh

install-nix: #if you dont have yet :)
	./src/ddd/Infrastructure/scripts/install-nix.sh

run-prod:
	nix develop .#devShells.x86_64-linux.prodShell

fix:
	php-cs-fixer fix ./src

migrate:
	./vendor/bin/phinx migrate

fixtures:
	./vendor/bin/phinx seed:run

test_ab:
	./src/ddd/Infrastructure/scripts/test_ab.php 'http://localhost:8000/post'

rabbitmq:
	docker-compose -f src/ddd/Infrastructure/Queue/RabbitMQ/docker-compose.yml up -d

mysql:
	docker-compose -f src/ddd/Infrastructure/db/docker-compose.yml up -d

behat:
	./vendor/bin/behat


.PHONY: run watch install-nix run-prod fix migrate fixtures test_ab mysql
