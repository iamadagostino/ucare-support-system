<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit4945093086bf31695a3648589ed85b77
{
    public static $prefixLengthsPsr4 = array (
        's' => 
        array (
            'smartcat\\' => 9,
        ),
        'S' => 
        array (
            'SmartcatSupport\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'smartcat\\' => 
        array (
            0 => __DIR__ . '/../..' . '/lib',
        ),
        'SmartcatSupport\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit4945093086bf31695a3648589ed85b77::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit4945093086bf31695a3648589ed85b77::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}