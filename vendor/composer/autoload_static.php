<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitfecc345e474e567a29bfc483a1916abb
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'PHPMailer\\PHPMailer\\' => 20,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'PHPMailer\\PHPMailer\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpmailer/phpmailer/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitfecc345e474e567a29bfc483a1916abb::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitfecc345e474e567a29bfc483a1916abb::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInitfecc345e474e567a29bfc483a1916abb::$classMap;

        }, null, ClassLoader::class);
    }
}
