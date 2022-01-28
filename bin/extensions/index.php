<?php

include __DIR__ . '/../../vendor/autoload.php';

/**
 * Обзорная информация по списку расширений и их функциям
 */

$extensions = get_loaded_extensions();

dump($extensions);
foreach ($extensions as $extension) {
    $funcs = get_extension_funcs($extension);

    if ($funcs) {
        echo sprintf("%s: %s\n", $extension, count($funcs));
    }

    /**
     * Более подробная информациям по всем деталям расширения
     */
    if ($extension === 'tokenizer') {
        $reflect = new ReflectionExtension($extension);
//        dump($reflect);
    }
}
