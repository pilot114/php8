<?php

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

class DateString
{
	public static function now(string $format = 'n/j/Y g:i a'): string
	{
		return date($format);
	}
}

class ServerException extends \Exception {};

class TcpServer
{
	protected string $host = '127.0.0.1';
	protected int $port = 80;
	protected int $socketTimeout = 3600;

	public function listen(): void
	{
		if (!$this->autoSelectPort()) {
			throw new ServerException('Не удалось автоматически выбрать порт');
		}

		$uri = sprintf("tcp://%s:%s", $this->host, $this->port);
		$socket = stream_socket_server($uri, $errno, $errstr);

		if (!$socket) {
            $message = sprintf("Не удалось забиндить сокет: %s (%s)", $errstr, $errno);
            throw new ServerException($message);
		}

        IO::write(sprintf("start server on %s:%s", $this->host, $this->port));

        while ($conn = stream_socket_accept($socket, $this->socketTimeout, $peerName)) {
            IO::write(sprintf('connected peer: %s', $peerName));
            $this->handle($conn);
            fclose($conn);
        }
        fclose($socket);
    }

    protected function handle($conn)
    {
        IO::write('HTTP/1.1 200 OK', $conn);
        IO::write('Content-Type: text/html; charset=utf-8', $conn);
        IO::write('', $conn);
        IO::write('local time ' . DateString::now(), $conn);
    }

	protected function autoSelectPort(): bool
	{
		$maxPort = 100;
		$connection = null;
		while (!is_resource($connection)) {
			$connection = fsockopen($this->host, $this->port);
			$this->port++;
			if ($this->port > $maxPort) {
				return false;
			}
		}
		fclose($connection);
		return true;
	}
}
