<?php

$port = 9050;

$sock = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
socket_set_option($sock, SOL_SOCKET, SO_REUSEADDR, 1);
socket_bind($sock, 0, $port);
socket_listen($sock);

$clients = [$sock];

while (true) {
    $read = $clients;

    $write = null;
    $except = null;
    if (socket_select($read, $write, $except, 0) < 1) {
        continue;
    }

    // welcome new client
    if (in_array($sock, $read)) {
        $clients[] = $newsock = socket_accept($sock);

        socket_write($newsock, "no noobs, but ill make an exception :)\n" .
            "There are " . (count($clients) - 1) . " client(s) connected to the server\n");
        socket_getpeername($newsock, $ip);

        echo "New client connected: {$ip}\n";

        $key = array_search($sock, $read);
        unset($read[$key]);
    }

    foreach ($read as $readSock) {
        // без попытки прочитать мы не можем понять что клиент отвалился
        $data = @socket_read($readSock, 1024, PHP_NORMAL_READ);

        // disconnect client
        if ($data === false) {
            $key = array_search($readSock, $clients);
            unset($clients[$key]);
            echo "client disconnected.\n";
            continue;
        }

        $data = trim($data);
        if (!empty($data)) {
            // broadcast
            foreach ($clients as $sendSock) {
                if ($sendSock == $sock || $sendSock == $readSock) {
                    continue;
                }
                socket_write($sendSock, $data . "\n");
            }
        }
    }
    usleep(10000);
}
