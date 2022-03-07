<?php

include './vendor/autoload.php';

use \Server\{
    IpcManager,
    HttpServer,
    Inotify,
};

$manager = new IpcManager([
    new HttpServer(),
    new Inotify(),
]);
$manager->run(IpcManager::IPC_SOCKET);