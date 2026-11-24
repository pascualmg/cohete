#!/usr/bin/env bash
# Regenera las imágenes del portfolio de Selena a partir de su PDF.
# Uso: tools/selena/regenerar.sh /ruta/al/portfolio.pdf
# Ojo: RULES de boards.py y los casos especiales de tiles.py están calibrados
# para el PDF de nov-2026 (17 páginas). Con un PDF nuevo, revisar con lines.py.
set -euo pipefail
PDF=$(realpath "$1")
HERE=$(cd "$(dirname "$0")" && pwd)
WEB="${WEB:-$HERE/../../src/ddd/Infrastructure/webserver/html/selena}"
WORK=$(mktemp -d); cd "$WORK"; mkdir -p out t
PY="nix-shell -p poppler-utils python3Packages.pdfplumber python3Packages.pillow python3Packages.scipy python3Packages.numpy --run"
$PY "pdftoppm -r 200 -png '$PDF' r200"
$PY "python3 '$HERE/boards.py' '$PDF' && python3 '$HERE/fixline.py' && python3 '$HERE/render.py' && python3 '$HERE/tiles.py' out t tiles.json"
cp out/*.webp "$WEB/img/"; cp t/*.webp "$WEB/img/t/"; cp tiles.json "$WEB/tiles.json"
echo "Listo en $WEB (trabajo en $WORK). Sube la versión ?v= en index.html y los imports."
