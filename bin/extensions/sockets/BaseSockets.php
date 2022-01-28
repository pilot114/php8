<?php

/**
 * Методы должны абсолютно повторять стандартный базовый интерфейс, кастомное только описание
 *
 * https://github.com/clue/socket-raw
 */
class BaseSockets
{
    /**
     * Запускает системный вызов select() для заданных массивов сокетов с указанным тайм-аутом
     * $read - выбирается сокет, не заблокированный для чтения
     * $write - выбирается сокет, не заблокированный для записи
     * $except - сокеты для исключений
     * - Вы всегда должны попытаться использовать socket_select() без тайм-аута
     * - Сокет не должен добавляться к любому из массивов, если вы не собираетесь проверять результат после вызова
     */
    protected function socketSelect(
        array|null &$read,
        array|null &$write,
        array|null &$except,
        int|null $seconds,
        int $microseconds = 0
    ): int|false
    {
        return socket_select($read, $write, $except, $seconds, $microseconds);
    }

    /**
     * Возвращает строку, описывающую ошибку сокета
     * системные сообщения, получаемые этой функцией, будут появляться в зависимости от текущей локали (LC_MESSAGES)
     */
    protected function socketStrerror(int $errorCode): string
    {
        return socket_strerror($errorCode);
    }

    /**
     * Возвращает последнюю ошибку на сокете
     */
    // define('ENOTSOCK',      88);    /* Socket operation on non-socket */
    // define('EDESTADDRREQ',  89);    /* Destination address required */
    // define('EMSGSIZE',      90);    /* Message too long */
    // define('EPROTOTYPE',    91);    /* Protocol wrong type for socket */
    // define('ENOPROTOOPT',   92);    /* Protocol not available */
    // define('EPROTONOSUPPORT', 93);  /* Protocol not supported */
    // define('ESOCKTNOSUPPORT', 94);  /* Socket type not supported */
    // define('EOPNOTSUPP',    95);    /* Operation not supported on transport endpoint */
    // define('EPFNOSUPPORT',  96);    /* Protocol family not supported */
    // define('EAFNOSUPPORT',  97);    /* Address family not supported by protocol */
    // define('EADDRINUSE',    98);    /* Address already in use */
    // define('EADDRNOTAVAIL', 99);    /* Cannot assign requested address */
    // define('ENETDOWN',      100);   /* Network is down */
    // define('ENETUNREACH',   101);   /* Network is unreachable */
    // define('ENETRESET',     102);   /* Network dropped connection because of reset */
    // define('ECONNABORTED',  103);   /* Software caused connection abort */
    // define('ECONNRESET',    104);   /* Connection reset by peer */
    // define('ENOBUFS',       105);   /* No buffer space available */
    // define('EISCONN',       106);   /* Transport endpoint is already connected */
    // define('ENOTCONN',      107);   /* Transport endpoint is not connected */
    // define('ESHUTDOWN',     108);   /* Cannot send after transport endpoint shutdown */
    // define('ETOOMANYREFS',  109);   /* Too many references: cannot splice */
    // define('ETIMEDOUT',     110);   /* Connection timed out */
    // define('ECONNREFUSED',  111);   /* Connection refused */
    // define('EHOSTDOWN',     112);   /* Host is down */
    // define('EHOSTUNREACH',  113);   /* No route to host */
    // define('EALREADY',      114);   /* Operation already in progress */
    // define('EINPROGRESS',   115);   /* Operation now in progress */
    // define('EREMOTEIO',     121);   /* Remote I/O error */
    // define('ECANCELED',     125);   /* Operation Canceled */
    protected function socketLastError(\Socket|null $socket = null): int
    {
        return socket_last_error($socket);
    }

    /**
     * Очищает ошибку на сокете или последний код ошибки
     */
    protected function socketClearError(\Socket|null $socket = null): void
    {
        socket_clear_error($socket);
    }

    /**
     * Запрашивает удалённую сторону указанного сокета, в результате может быть возвращён хост/порт
     * или путь в файловой системе Unix, в зависимости от типа сокета
     * - socket_getpeername() не должна быть использована с сокетами AF_UNIX созданными при помощи функции socket_connect()
     * - Для того, чтобы socket_getpeername() вернула осмысленное значение, сокет, к которому она применяется,
     * должен понимать концепцию "равных отношений" (peer).
     */
    protected function socketGetpeername(\Socket $socket, string &$address, int &$port = null): bool
    {
        return socket_getpeername($socket, $address, $port);
    }

    /**
     * Запрашивает локальную сторону указанного сокета, в результате может быть возвращён хост/порт
     * или путь в файловой системе Unix, в зависимости от типа сокета
     * - socket_getpeername() не должна быть использована с сокетами AF_UNIX созданными при помощи функции socket_connect()
     */
    protected function socketGetsockname(\Socket $socket, string &$address, int &$port = null): bool
    {
        return socket_getsockname($socket, $address, $port);
    }

    /**
     * Создаёт и возвращает экземпляр Socket, также называемый как конечная точка обмена информацией.
     * Типичное сетевое соединение состоит из двух сокетов, один из которых выполняет роль клиента,
     * а другой выполняет роль сервера.
     *
     * $domain   - AF_INET (IPv4) | AF_INET6 (IPv6) | AF_UNIX (последнее отлично подходит для IPC)
     * $type     - SOCK_STREAM (основной для TCP) | SOCK_DGRAM (UDP) | SOCK_SEQPACKET | SOCK_RAW | SOCK_RDM (обычно не реализовано в ОС)
     * $protocol - getprotobyname(icmp | udp | tcp) (согласно /etc/protocols) | SOL_TCP | SOL_UDP
     */
    protected function socketCreate(int $domain, int $type, int $protocol): \Socket|false
    {
        return socket_create($domain, $type, $protocol);
    }

    /**
     * Открывает сокет на указанном порту для принятия соединений
     *
     * создаёт новый экземпляр Socket типа AF_INET, слушающий на всех локальных интерфейсах указанный порт в ожидании новых соединений.
     * Эта функция предназначена для упрощения задачи создания нового сокета, который только слушает порт для получения новых соединений
     *
     * Параметр backlog определяет максимальную длину, до которой может вырасти очередь ожидающих соединений
     */
    protected function socketCreateListen(int $port, int $backlog = 128): \Socket|false
    {
        return socket_create_listen($port, $backlog);
    }

    /**
     * создаёт два соединённых и неразличимых сокета, и сохраняет их в массиве pair. (array \Socket)
     * Эта функция обычно используется IPC (межпроцессном взаимодействии).
     */
    protected function socketCreatePair(
        int $domain,
        int $type,
        int $protocol,
        array &$pair
    ): bool
    {
        return socket_create_pair($domain, $type, $protocol, $pair);
    }

    /**
     * Принимает соединение на сокете
     *
     * После того, как сокет socket был создан при помощи функции socket_create(),
     * привязан к имени при помощи функции socket_bind(),
     * и ему было указано слушать соединения при помощи функции socket_listen(),
     * эта функция будет принимать входящие соединения на этом сокете
     *
     * Если в очереди сокета есть несколько соединений, будет использовано первое из них. Если нет ожидающих соединений,
     * то функция socket_accept() будет блокировать выполнение скрипта до тех пор, пока не появится соединение.
     * Если сокет socket был сделан неблокирующим при помощи функции socket_set_blocking() или socket_set_nonblock(),
     * будет возвращено false
     *
     * Экземпляр Socket, полученный при помощи функции socket_accept() не может быть использован для принятия новых соединений.
     * Однако изначальный слушающий сокет socket, остаётся открытым и может быть использован повторно.
     */
    protected function socketAccept(\Socket $socket): \Socket|false
    {
        return socket_accept($socket);
    }

    /**
     * Устанавливает неблокирующий режим на сокете
     *
     * Когда операция (например, получение, отправка, соединение, принятие соединения, ...) выполняется на неблокирующем сокете,
     * скрипт не будет приостанавливать своё исполнение до получения сигнала или возможности выполнить операцию.
     * Если выполняемая операция должна привести к блокированию выполнения скрипта, то вместо этого вызываемая функция возвратит ошибку.
     */
    protected function socketSetNonblock(\Socket $socket): bool
    {
        return socket_set_nonblock($socket);
    }

    /**
     * Устанавливает блокирующий режим на сокете
     *
     * (socket_accept() зависнет до тех пор, пока не будет принято соединение)
     */
    protected function socketSetBlock(\Socket $socket): bool
    {
        return socket_set_block($socket);
    }
}