<?php

if (!$loader = include __DIR__.'/../vendor/.composer/autoload.php') {
    $nl = PHP_SAPI === 'cli' ? PHP_EOL : '<br />';
    echo "$nl$nl";
    if (is_writable(dirname(__DIR__)) && $installer = @file_get_contents('http://getcomposer.org/installer')) {
        echo 'Debes configurar las dependencias del proyecto.'.$nl;
        $installerPath = dirname(__DIR__).'/install-composer.php';
        file_put_contents($installerPath, $installer);
        echo 'El instalador composer se descargó en '.$installerPath.$nl;
        die('Ejecuta la siguiente orden en '.dirname(__DIR__).':'.$nl.$nl.
            'php install-composer.php'.$nl.
            'php composer.phar install'.$nl);
    }
    die('Debes configurar las dependencias del proyecto.'.$nl.
        'Ejecuta las siguientes ordenes en '.dirname(__DIR__).':'.$nl.$nl.
        'curl -s http://getcomposer.org/installer | php'.$nl.
        'php composer.phar install'.$nl);
}

use Doctrine\Common\Annotations\AnnotationRegistry;

// intl
if (!function_exists('intl_get_error_code')) {
    require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

    $loader->add('', __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
}

AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

// Swiftmailer necesita un autocargador especial para permitir la carga
// diferida de el archivo init (lo cual es muy costoso)
require_once __DIR__.'/../vendor/swiftmailer/swiftmailer/lib/classes/Swift.php';
Swift::registerAutoload(__DIR__.'/../vendor/swiftmailer/swiftmailer/lib/swift_init.php');
