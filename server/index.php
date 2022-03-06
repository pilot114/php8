<?php

include "./Server.php";

class Inotify
{
    protected function watch($file = __FILE__)
    {
        $fd = inotify_init();
        $watch_descriptor = inotify_add_watch($fd, $file, IN_ALL_EVENTS);

        $events = inotify_read($fd);
        print_r($events);
    }
}

class HttpServer extends TcpServer
{
    protected function handle($conn)
    {
        // для совместимости: выставить !is_cli и глобальные массивы
        IO::write('HTTP/1.1 200 OK', $conn);
        IO::write('Content-Type: text/html; charset=utf-8', $conn);
        IO::write('', $conn);
        IO::write('hello world', $conn);
    }
}

//$server = new HttpServer();
//$server->listen();

// HandlerInterface - open(запуск while(true)), handle(вызывается на событии внутри цикла), close(очистить)
//$server = new AsyncServer(AsyncServer::FORK, AsyncServer::IPC_SOCKET);
//$server->addHandlers([
//    HandlerInterface
//]);