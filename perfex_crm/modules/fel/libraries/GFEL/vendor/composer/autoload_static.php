<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit6076363ad217a06e4b7614adb093163a
{
    public static $prefixLengthsPsr4 = array (
        'J' => 
        array (
            'Juanj\\Gfel\\' => 11,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Juanj\\Gfel\\' => 
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
            $loader->prefixLengthsPsr4 = ComposerStaticInit6076363ad217a06e4b7614adb093163a::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit6076363ad217a06e4b7614adb093163a::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit6076363ad217a06e4b7614adb093163a::$classMap;

        }, null, ClassLoader::class);
    }
}
