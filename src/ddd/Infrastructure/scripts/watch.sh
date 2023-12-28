#!/usr/bin/env nix-shell
#!nix-shell -i bash -p inotify-tools

DIRECTORY_TO_OBSERVE="../"
MAKEFILE_DIRECTORY="../../"

make -C $MAKEFILE_DIRECTORY run &
pid=$!


while true; do
    change=$(inotifywait -r -e modify,create,delete $DIRECTORY_TO_OBSERVE)
    kill $pid
    make -C $MAKEFILE_DIRECTORY run &
    pid=$!
done