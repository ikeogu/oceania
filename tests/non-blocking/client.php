#!/usr/bin/php
<?php
if (($sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP)) === false) {
    echo "socket_create() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}

if (socket_connect($sock, '127.0.0.1', 10000) === false) {
    echo "socket_connect() failed: reason: " . socket_strerror(socket_last_error()) . "\n";
}

do {

    $stdin = fopen('php://stdin', 'r');
    $msg = trim(fgets($stdin));
    socket_write($sock, $msg, strlen($msg));

    if (false === ($buf = socket_read($sock, 2048, PHP_NORMAL_READ))) {
        echo "socket_read() failed: reason: " . socket_strerror(socket_last_error($sock)) . "\n";
    }

    echo "response: $buf \n";

} while (true);


// close socket
socket_close($sock);
?>
