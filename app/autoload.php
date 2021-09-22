<?php

use Doctrine\Common\Annotations\AnnotationRegistry;
use Composer\Autoload\ClassLoader;
use Symfony\Component\Debug\Debug;
/**
 * @var ClassLoader $loader
 */
$loader = require __DIR__.'/../vendor/autoload.php';
Debug::enable();
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->add('', __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
}

$loader->add('PHPExcel', __DIR__.'/../vendor/PHPExcel/lib');
$loader->add('WhiteOctober', __DIR__.'/../vendor/bundles');

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

return $loader;
