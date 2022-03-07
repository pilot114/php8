<?php

namespace Server;

/**
 * Класс, управляющий потоками выполнения и их взаимодействием
 * TODO: https://www.php.net/manual/ru/intro.parallel.php
 */
class IpcManager
{
    const IPC_SOCKET = 1;
    const IPC_SHM = 2;

    protected $watchers;

    public function __construct(array $watchers)
    {
        $this->watchers = $watchers;
    }

    public function run(int $type)
    {
        if ($type === self::IPC_SOCKET) {
            $this->handleSocket();
        }
        if ($type === self::IPC_SHM) {
            $this->handleSharedMemory();
        }
    }

    protected function handleSocket()
    {
        $watchers = $this->watchers;
        $socketPairs = [];
        /**
         * @var $watcher WatcherInterface
         */
        foreach ($watchers as $watcher) {
            // у процессов будут разные переменные (т.к. области памяти разные), но общие внешние дескрипторы
            socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $pair);
            $pid = pcntl_fork();
            if (!$pid) {
                // TODO: use $pair[1] for connect
                $watcher->open();
            }
            $socketPairs[] = $pair[0];
        }

        // более эффективная альтернатива declare_ticks
        pcntl_async_signals(true);
        pcntl_signal(SIGINT, function () use ($socketPairs, $watchers) {
            IO::write('close handleSocket');
            /**
             * @var $watcher WatcherInterface
             */
            foreach ($watchers as $watcher) {
                $watcher->close();
            }
            foreach ($socketPairs as $socketPair) {
                socket_close($socketPair[0]);
                socket_close($socketPair[1]);
                die();
            }
        });
    }

    protected function handleSharedMemory()
    {

    }
}