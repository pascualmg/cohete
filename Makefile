run:
	php "src/bootstrap.php"

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

.PHONY: run watch install-nix run-prod fix migrate fixtures test_ab