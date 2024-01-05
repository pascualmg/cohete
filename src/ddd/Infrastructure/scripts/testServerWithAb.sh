#!/usr/bin/env nix-shell
#!nix-shell -i bash -p apacheHttpd

# Store the first argument in the variable 'server', etc.
server="$1"
port="$2"
route="$3"

# Check that all arguments have been provided
if [[ -z "$server" ]] || [[ -z "$port" ]] || [[ -z "$route" ]]; then
    echo "Usage: ./run-ab.sh <server> <port> <route>"
       read -p "Would you like to use the default values (localhost 8000 /)? [Y/n] " answer
       if [[ $answer =~ ^[Yy]$ ]] || [[ -z $answer ]]; then
           server="localhost"
           port="8000"
           route="/"
       else
         echo "NOOP"
         exit 1
       fi
fi

# Execute ab with the provided server, port and route values
ab -n 100 -c 10 "http://$server:$port$route"