run:
	php "src/bootstrap.php"

watch:
	./src/scripts/watch.sh

install-nix: #if you dont have yet :)
	curl -L https://nixos.org/nix/install | sh -s -- --daemon

.PHONY: run watch install-nix