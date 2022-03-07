<?php

namespace Server;

/**
 * Для обработчиков событий, запускамых паралельно
 */
interface WatcherInterface
{
    // запуск цикла
    public function open(Channel $chan): void;
    // обработчик запускаемый в цикле
    public function handle($data): void;
    // очистка ресурсов
    public function close(): void;
}