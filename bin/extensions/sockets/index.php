<?php

include __DIR__ . '/../../../vendor/autoload.php';

//$reflect = new ReflectionExtension('sockets');
$funcs = get_extension_funcs('sockets');
dump($funcs);
