<?php

namespace Kuperwood\Eav;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

class Container
{
    private static ?Dependency $instance = null;

    public static function getInstance()
    {
        if (self::$instance === null) {
            self::$instance = is_null(self::$instance)
                ? self::instance()
                : self::$instance;
        }
        return self::$instance;
    }

    private static function instance(): Dependency
    {
        $dependency = new Dependency();
        $filesystem = new Filesystem();
        $ns = 'lang';
        $group = 'validation';
        $locale = 'en';
        $path = dirname(__DIR__) . '/vendor/illuminate/translation/lang';
        $loader = new FileLoader($filesystem, $path);
        $loader->addNamespace($ns, $path);
        $loader->load($locale, $group, $ns);
        $dependency->setValidator($loader, $locale);
        return $dependency;
    }

    public static function setInstance(Dependency $dependency)
    {
        self::$instance = $dependency;
    }
}