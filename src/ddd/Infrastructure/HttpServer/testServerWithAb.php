#!/usr/bin/env nix-shell
#!nix-shell -i php -p php apacheHttpd

<?php

function prompt($message) {
    echo "$message ";
    return trim(fgets(STDIN));
}

// Check if interactive flag is set
$isInteractive = in_array('-i', $GLOBALS['argv'], true);

$server = $GLOBALS['argv'][1] ?? null;
$port = $GLOBALS['argv'][2] ?? null;
$route = $GLOBALS['argv'][3] ?? null;

if ($isInteractive || empty($server) || empty($port) || empty($route)) {
    $server = prompt('Enter server') ?: 'localhost';
    $port = prompt('Enter port') ?: '8000';
    $route = prompt('Enter route') ?: '/';
}

// Execute ab with the provided server, port, and route values
$command = "ab -n 100 -c 10 http://$server:$port$route";
exec($command);
?>