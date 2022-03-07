<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit1f40a89f4d9ea03fc016e8cc0a2c2f85
{
    public static $prefixLengthsPsr4 = array (
        'S' => 
        array (
            'Server\\' => 7,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Server\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit1f40a89f4d9ea03fc016e8cc0a2c2f85::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit1f40a89f4d9ea03fc016e8cc0a2c2f85::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit1f40a89f4d9ea03fc016e8cc0a2c2f85::$classMap;

        }, null, ClassLoader::class);
    }
}