<?php
/**
 * This file is part of the eav package.
 * @author    Aleksandr Drobotik <drobotiksbox@gmail.com>
 * @copyright 2023 Aleksandr Drobotik
 * @license   https://opensource.org/license/mit  The MIT License
 */
declare(strict_types=1);

namespace Drobotik\Eav;

use Illuminate\Filesystem\Filesystem;
use Illuminate\Translation\FileLoader;

class DependencyManager
{
    private static ?DependencyContainer $instance = null;

    public static function getContainer()
    {
        if (self::$instance === null) {
            self::$instance = is_null(self::$instance)
                ? self::getDefaultContainer()
                : self::$instance;
        }
        return self::$instance;
    }

    public static function setContainer(DependencyContainer $dependency)
    {
        self::$instance = $dependency;
    }

    public static function getDefaultContainer(): DependencyContainer
    {
        $dependency = new DependencyContainer();
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
}