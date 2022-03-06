<?php

function tail($file, &$pos) {
    if(!$pos) $pos = filesize($file);
    $fd = inotify_init();
    $watch_descriptor = inotify_add_watch($fd, $file, IN_ALL_EVENTS);
    while (true) {
        $events = inotify_read($fd);
        foreach ($events as $event=>$evdetails) {
            switch (true) {
                case ($evdetails['mask'] & IN_MODIFY):
                    inotify_rm_watch($fd, $watch_descriptor);
                    fclose($fd);
                    $fp = fopen($file,'r');
                    if (!$fp) {
                        return false;
                    }
                    fseek($fp,$pos);
                    while (!feof($fp)) {
                        $buf .= fread($fp,8192);
                    }
                    $pos = ftell($fp);
                    fclose($fp);
                    return $buf;
                case ($evdetails['mask'] & IN_MOVE):
                case ($evdetails['mask'] & IN_MOVE_SELF):
                case ($evdetails['mask'] & IN_DELETE):
                case ($evdetails['mask'] & IN_DELETE_SELF):
                    inotify_rm_watch($fd, $watch_descriptor);
                    fclose($fd);
                    return false;
            }
        }
    }
}

$file = fopen(__FILE__);

$lastpos = 0;
while (true) {
    echo tail($file, $lastpos);
}