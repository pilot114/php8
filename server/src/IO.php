<?php

namespace Server;

class IO
{
    public static function write(string $message, $resource = \STDOUT)
    {
        fwrite($resource, $message . "\n");
    }

    public static function read($resource = \STDIN): string
    {
        return fread($resource, 1024);
    }
}