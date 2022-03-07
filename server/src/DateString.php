<?php

namespace Server;

class DateString
{
    public static function now(string $format = 'n/j/Y g:i a'): string
    {
        return date($format);
    }
}