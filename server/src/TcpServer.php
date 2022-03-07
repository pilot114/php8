<?php

namespace Server;

class TcpServer implements WatcherInterface
{
	protected string $host = '127.0.0.1';
	protected int $port = 80;
	protected int $socketTimeout = 3600;

	protected $socket;
	protected $chan;

	public function open(Channel $chan): void
	{
        $this->chan = $chan;
		if (!$this->autoSelectPort()) {
			throw new ServerException('Failed to automatically select port');
		}

		$uri = sprintf("tcp://%s:%s", $this->host, $this->port);
		$this->socket = stream_socket_server($uri, $errno, $errstr);

		if (!$this->socket) {
            $message = sprintf("Failed to bind socket: %s (%s)", $errstr, $errno);
            throw new ServerException($message);
		}

        IO::write(sprintf("Start server on %s:%s", $this->host, $this->port));

        while ($conn = stream_socket_accept($this->socket, $this->socketTimeout, $peerName)) {
            IO::write(sprintf('Connected peer: %s', $peerName));
            $this->handle($conn);
            fclose($conn);
        }
    }

    public function handle($conn): void
    {
        IO::write('HTTP/1.1 200 OK', $conn);
        IO::write('Content-Type: text/html; charset=utf-8', $conn);
        IO::write('', $conn);
        IO::write('local time ' . DateString::now(), $conn);
    }

    public function close(): void
    {
        fclose($this->socket);
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
