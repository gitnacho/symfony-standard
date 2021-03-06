<?php

use Symfony\Component\HttpFoundation\Request;

// Si no quieres configurar los permisos de la manera convencional sólo quita el
// comentario de la siguiente línea PHP, para más información lee
// http://gitnacho.github.com/symfony-docs-es/book/installation.html#instalando-y-configurando-symfony
// umask(0000);

// Esto previene el acceso para depurar los controladores frontales que se
    // despliegan por accidente a los servidores de producción.
// Siéntete libre de quitarlo, extenderlo, o hacer algo mucho más sofisticado.
if (isset($_SERVER['HTTP_CLIENT_IP'])
    || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
    || !in_array(@$_SERVER['REMOTE_ADDR'], array('127.0.0.1', 'fe80::1', '::1'))
) {
    header('HTTP/1.0 403 Prohibido');
    exit('No tienes permitido acceder a este archivo. Consulta '.basename(__FILE__).' para más información.');
}

$loader = require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
