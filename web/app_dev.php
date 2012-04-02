<?php

// Si no quieres configurar los permisos de la manera convencional sólo quita el
// comentario de la siguiente línea PHP
//umask(0000);
// lee http://gitnacho.github.com/symfony-docs-es/book/installation.html#instalando-y-configurando-symfony
// para más información

// Esto previene el acceso para depurar los controladores frontales que se
// despliegan por accidente a los servidores de producción.
// Siéntete libre de quitarlo, extenderlo, o hacer algo mucho más sofisticado.
if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    header('HTTP/1.0 403 Prohibido');
    exit('No tienes permitido acceder a este archivo. Consulta '.basename(__FILE__).' para más información.');
}

require_once __DIR__.'/../app/bootstrap.php.cache';
require_once __DIR__.'/../app/AppKernel.php';

use Symfony\Component\HttpFoundation\Request;

$kernel = new AppKernel('dev', true);
$kernel->loadClassCache();
$request = Request::createFromGlobals();
$response = $kernel->handle($request);
$response->send();
$kernel->terminate($request, $response);
