<?php

namespace Server;

/**
 * Абстракция, через которою можно передавать данные между процессами
 */
class Channel
{
    public function read(): string
    {
        return 'qwe';
    }

    public function write(string $message)
    {

    }
}