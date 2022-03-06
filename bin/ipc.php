<?php

// https://sudonull.com/post/122352-PHP-IPC-Interprocess-Communication-in-PHP
// https://habr.com/ru/post/40432/
/**
 * 1. Паралельный запуск бесконечных циклов (PCNTL parallel Eio Ev Expect POSIX pht Sync pthreads)
 * 2. Обмен данными между ними (сокеты / разделяемая память / семафоры)
 */


/**
 * Sockets - гибкий, но не самый эффективный и удобный способ
 */
/*
// у процессов будут разные переменные (т.к. области памяти разные), но общие внешние дескрипторы
socket_create_pair(AF_UNIX, SOCK_STREAM, 0, $pair);
$pid = pcntl_fork();
// родитель
if ($pid) {
    // более эффективная льтернатива declare_ticks
    pcntl_async_signals(true);
    pcntl_signal(SIGINT, function () use ($pair) {
        echo "close\n";
        socket_close($pair[0]);
        socket_close($pair[1]);
        die();
    });

    while(true) {
        $message = '1';
        socket_write($pair[0], $message, strlen($message));
        $reply = socket_read($pair[0], strlen($message));
        echo "one recive: $reply\n";
        sleep(1);
    }
}

while(true) {
    $message = '2';
    socket_write($pair[1], $message, strlen($message));
    $reply = socket_read($pair[1], strlen($message));
    echo "two recive: $reply\n";
    sleep(1);
}
*/

/**
 * Shared Memory + Семафоры
 */

$semKey = ftok( __FILE__, 'b' );
$semId = sem_get( $semKey );
$shmKey = ftok( __FILE__, 'm' );
$shmId = shm_attach($shmKey, 1024);

const SHM_VAR = 1;

$pid = pcntl_fork();
if ($pid) {
    pcntl_async_signals(true);
    pcntl_signal(SIGINT, function () use ($shmId) {
        echo "close\n";
        shm_remove($shmId);
        shm_detach($shmId);
        die();
    });

    while(true) {
        $message = '1';

        sem_acquire($semId);
        if (shm_has_var($shmId, SHM_VAR)){
            $reply = shm_get_var($shmId, SHM_VAR);
            echo "one recive: $reply\n";
        }
        shm_put_var($shmId, SHM_VAR, $message);
        sem_release($semId);
        sleep(1);
    }
}

while(true) {
    $message = '2';

    sem_acquire($semId);
    if (shm_has_var($shmId, SHM_VAR)){
        $reply = shm_get_var($shmId, SHM_VAR);
        echo "two recive: $reply\n";
    }
    shm_put_var($shmId, SHM_VAR, $message);
    sem_release($semId);
    sleep(1);
}