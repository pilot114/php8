<?php

$server = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
if (!$server) {
    die("Failed to start event server. socket_create: " . socket_strerror(socket_last_error()) . "\n");
}
if (!socket_set_option($server, SOL_SOCKET, SO_REUSEADDR, 1)) {
    die("Failed to start event server. socket_set_option: " . socket_strerror(socket_last_error()) . "\n");
}
if (!socket_bind($server, "0.0.0.0", 8087)) {
    die("Failed to start event server. socket_bind: " . socket_strerror(socket_last_error()) . "\n");
}
if (!socket_listen($server, 10)) {
    die("Failed to start event server. socket_listen: " . socket_strerror(socket_last_error()) . "\n");
}

while (true) {
    $read = [$server];
    $write = null;
    $except = null;
    $ready = socket_select($read, $write, $except, 0);
    if ($ready === false) {
        die("Failed to listen for clients: " . socket_strerror(socket_last_error()));
    }

    if ($ready > 0) {
        $client = socket_accept($server);

        $input = socket_read($client, 1024);
        if ($input) {
            $request = parse($input);
            socket_getpeername($client, $request['ip']);

            // TODO: correct response
            if (strpos($request['path'], ".html") && file_exists(__DIR__ . $request['path'])) {
                $response = [
                    $request['protocol'] . " 200 OK",
                    "Content-type: text/html; charset=utf-8",
                    file_get_contents(__DIR__ . $request['path']),
                ];
            } elseif ($request['path'] == "/test") {
                $response = [
                    "<!DOCTYPE HTML><html><head><html><body><h1>Its working!</h1>Have fun",
                    "<pre>Request: " . print_r($request, true) . "</pre>",
                    "</body></html>",
                ];
            } else {
                $response = [
                    $request['protocol'] . " 404 Not Found"
                ];
            }
            socket_write($client, implode("\r\n", $response) . "\r\n");
            socket_close($client);

            print_r($request);
            print_r($response);
        }
    }
    // чтобы цикл не нагружал cpu
    usleep(10000);
}
// закроется сам
// socket_close($server);


function parse($input)
{
    $request = [];
    $line = explode("\n", preg_replace('/[^A-Za-z0-9\-+\n :;=%*&?.,\/_]/', '', substr($input, 0, 2000)));

    [$request["method"], $request["url"], $request["protocol"]] = explode(" ", $line[0]);
    unset($line[0]);

    $headers = [];
    foreach ($line as $l) {
        if (!str_contains($l, ": ")) {
            continue;
        }
        [$key, $val] = explode(": ", $l);
        if ($key) {
            $headers[strtolower($key)] = $val;
        }
    }
    $request['headers'] = $headers;
    $request += (array)parse_url($request['url']);
    parse_str($request['query'] ?? '', $request['query']);
    return $request;
}