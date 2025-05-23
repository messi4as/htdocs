<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitc39b301019c3aff32e1e11bd7f500077
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPJasper\\' => 10,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPJasper\\' => 
        array (
            0 => __DIR__ . '/..' . '/geekcom/phpjasper/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitc39b301019c3aff32e1e11bd7f500077::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitc39b301019c3aff32e1e11bd7f500077::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitc39b301019c3aff32e1e11bd7f500077::$classMap;

        }, null, ClassLoader::class);
    }
}
