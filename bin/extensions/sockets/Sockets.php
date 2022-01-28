<?php

/**
 * Класс для расширения сокетов
 * 0 => "socket_select"
 *
 * 25 => "socket_last_error"
 * 13 => "socket_strerror"
 * 26 => "socket_clear_error"
 *
 * 9 => "socket_getsockname"
 * 10 => "socket_getpeername"
 *
 * 11 => "socket_create"
 * 1 => "socket_create_listen"
 * 23 => "socket_create_pair"
 *
 * 14 => "socket_bind"
 * 5 => "socket_listen"
 * 2 => "socket_accept"
 *
 * 3 => "socket_set_nonblock"
 * 4 => "socket_set_block"
 *
 * 6 => "socket_close"
 *
 * 32 => "socket_addrinfo_lookup"
 * 33 => "socket_addrinfo_connect"
 * 34 => "socket_addrinfo_bind"
 * 35 => "socket_addrinfo_explain"
 *
 * 7 => "socket_write"
 * 8 => "socket_read"
 *
 * 12 => "socket_connect"
 * 15 => "socket_recv"
 * 16 => "socket_send"
 * 17 => "socket_recvfrom"
 * 18 => "socket_sendto"
 * 19 => "socket_get_option"
 * 20 => "socket_getopt"
 * 21 => "socket_set_option"
 * 22 => "socket_setopt"
 * 24 => "socket_shutdown"
 * 27 => "socket_import_stream"
 * 28 => "socket_export_stream"
 * 29 => "socket_sendmsg"
 * 30 => "socket_recvmsg"
 * 31 => "socket_cmsg_space"
 */
class Sockets extends BaseSockets
{
    /**
     * Тесты ???
     */
    public function pair()
    {
        $sockets = [];
        $domain = (strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? AF_INET : AF_UNIX);

        if ($this->socketCreatePair($domain, SOCK_STREAM, 0, $sockets) === false) {
            echo "Не получилось выполнить socket_create_pair. Причина: " . $this->error();
        }
        if ($this->socketWrite($sockets[0], "ABCdef123\n", strlen("ABCdef123\n")) === false) {
            echo "Не получилось выполнить socket_write(). Причина: " . $this->error($sockets[0]);
        }
        if (($data = $this->socketRead($sockets[1], strlen("ABCdef123\n"), PHP_BINARY_READ)) === false) {
            echo "Не получилось выполнить socket_read(). Причина: " . $this->error($sockets[1]);
        }
        var_dump($data);

        $this->socketClose($sockets[0]);
        $this->socketClose($sockets[1]);
    }

    public function pairIPC()
    {
        $arr = [];
        if ($this->socketCreatePair(AF_UNIX, SOCK_STREAM, 0, $arr) === false) {
            echo "Не получилось выполнить socket_create_pair(). Причина: " . $this->error();
        }
        $pid = pcntl_fork();
        if ($pid == -1) {
            echo 'Не могу создать новый процесс.';
            return false;
        }
        [$parent, $child] = $arr;

        $strone = 'Сообщение от родительского процесса.';
        $strtwo = 'Сообщение от дочернего процесса.';
        if ($pid) {
            $this->socketClose($parent);
            if ($this->socketWrite($child, $strone, strlen($strone)) === false) {
                echo "Не получилось выполнить socket_write(). Причина: " . $this->error($child);
            }
            if ($this->socketRead($child, strlen($strtwo), PHP_BINARY_READ) == $strtwo) {
                echo "Получено $strtwo\n";
            }
            $this->socketClose($child);
        } else {
            $this->socketClose($child);
            if ($this->socketWrite($parent, $strtwo, strlen($strtwo)) === false) {
                echo "Не получилось выполнить socket_write(). Причина: " . $this->error($parent);
            }
            if ($this->socketRead($parent, strlen($strone), PHP_BINARY_READ) == $strone) {
                echo "Получено $strone\n";
            }
            $this->socketClose($parent);
        }
    }

    public function listenNotBlocking()
    {
        $clients = [];
        $socket = $this->socketCreate(AF_INET, SOCK_STREAM, SOL_TCP);
        $this->socketBind($socket, '127.0.0.1', '8087');
        $this->socketListen($socket);
        $this->socketSetNonblock($socket);

        while (true) {
            if (($newc = $this->socketAccept($socket)) !== false) {
                echo "Client $newc has connected\n";
                $clients[] = $newc;
            }
        }
    }

    public function select()
    {
        $socket1 = $this->socketCreate(AF_UNIX, SOCK_STREAM, 0);
        $socket2 = $this->socketCreate(AF_UNIX, SOCK_STREAM, 0);
        $read = [$socket1, $socket2];
        $write = null;
        $except = null;
        $numChangedSockets = $this->socketSelect($read, $write, $except, 0);

        if ($numChangedSockets === false) {
            // типичная обработка ошибки
            echo "Неудачный вызов socket_select(), причина: " . $this->error() . "\n";
        }
        if ($numChangedSockets > 0) {
            echo $numChangedSockets . "\n";
        }
    }

    protected function error($socket = null): string
    {
        $info = $this->socketStrerror($this->socketLastError($socket));
        $this->socketClearError($socket);
        return $info;
    }
    /**
     * TODO: для внешнего апи нужно многое задавать по умолчанию, избегать некорректных ситуаций (закрытие или незакрытие соединений),
     * обрабатывать любые ошибки как эксепшены, дружелюбный интерфейс.
     * Нужно понимать список кейсов, для выбора - статика / инстанцирование
     */
}