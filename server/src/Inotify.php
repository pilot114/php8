<?php

namespace Server;

class Inotify implements WatcherInterface
{
    protected $fd;
    protected $watchDescriptor;
    protected $chan;

    public function open(Channel $chan): void
    {
        $this->chan = $chan;
        $path = '.';
        $this->fd = inotify_init();
        $this->watchDescriptor = inotify_add_watch($this->fd, $path, IN_MODIFY);
        IO::write('Start file watcher');

        while (true) {
            $events = inotify_read($this->fd);
            foreach ($events as $event => $evdetails) {
                $this->handle($evdetails['name']);
            }
        }
    }

    public function handle($data): void
    {
        // $this->chan

        if (is_string($data)) {
            IO::write('Inotify: ' . $data);
        }
    }

    public function close(): void
    {
        inotify_rm_watch($this->fd, $this->watchDescriptor);
    }
}