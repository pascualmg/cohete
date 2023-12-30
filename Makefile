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

.PHONY: run watch install-nix run-prod fix migrate