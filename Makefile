run:
	php "src/bootstrap.php"

watch:
	./src/scripts/watch.sh

install-nix: #if you dont have yet :)
	./src/scripts/install-nix.sh

run-prod:
	nix develop .#devShells.x86_64-linux.prodShell

.PHONY: run watch install-nix run-prod