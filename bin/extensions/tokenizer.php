<?php

include __DIR__ . '/../../vendor/autoload.php';

$code = file_get_contents(__FILE__);

$tokens = token_get_all($code);

foreach ($tokens as $token) {
    if (is_array($token)) {
        $token = [
            'name' => token_name($token[0]),
            'value' => $token[1],
            'line' => $token[2],
        ];
        dump($token);
    }
}
dump(count($tokens));
