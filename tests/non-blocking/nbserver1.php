#!/usr/bin/php
<?php
$host = "127.0.0.1";
$port = 10000;

set_time_limit(0);

$socket = socket_create(AF_INET, SOCK_STREAM, 0) or die("Could not create socket\n");

$result = socket_bind($socket, $host, $port) or die("Could not bind to socket\n");

$result = socket_listen($socket, 3) or die("Could not set up socket listener\n");


do {
    $spawn = socket_accept($socket) or die("Could not accept incoming connection\n");
    do {
        $input = socket_read($spawn, 1024) or die("Could not read input\n");

        $input = trim($input);
        echo "Client Message : " . $input . "\n";

        $response = 'received' . "\n";
        socket_write($spawn, $response, strlen($response)) or die("Could not write output\n");

        if ($input == 'quit') {
            break;
        }
        if ($input == 'shutdown') {
            socket_close($spawn);
            break 2;
        }

    } while (true);
} while (true);
socket_close($socket);
?>
