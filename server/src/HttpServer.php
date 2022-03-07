<?php

namespace Server;

class HttpServer extends TcpServer
{
    public function handle($conn): void
    {
//        $this->chan

        // для совместимости: выставить !is_cli и глобальные массивы
        IO::write('HTTP/1.1 200 OK', $conn);
        IO::write('Content-Type: text/html; charset=utf-8', $conn);
        IO::write('', $conn);
        IO::write('hello world', $conn);
    }
}