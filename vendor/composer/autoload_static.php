<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit12c70d6e4d8831726a95675c62a4dd84
{
    public static $prefixLengthsPsr4 = array (
        'C' => 
        array (
            'Composer\\Installers\\' => 20,
            'CP_Staff\\' => 9,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Composer\\Installers\\' => 
        array (
            0 => __DIR__ . '/..' . '/composer/installers/src/Composer/Installers',
        ),
        'CP_Staff\\' => 
        array (
            0 => __DIR__ . '/../..' . '/includes',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit12c70d6e4d8831726a95675c62a4dd84::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit12c70d6e4d8831726a95675c62a4dd84::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit12c70d6e4d8831726a95675c62a4dd84::$classMap;

        }, null, ClassLoader::class);
    }
}
