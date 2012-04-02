<?php

if (!isset($_SERVER['HTTP_HOST'])) {
    exit('Este programa no se puede ejecutar desde la CLI. Ejecútalo desde un navegador.');
}

if (!in_array(@$_SERVER['REMOTE_ADDR'], array(
    '127.0.0.1',
    '::1',
))) {
    header('HTTP/1.0 403 Prohibido');
    exit('Este programa únicamente es accesible a través de "localhost".');
}

$majorProblems = array();
$minorProblems = array();
$phpini = false;

// mínimo
if (!version_compare(phpversion(), '5.3.2', '>=')) {
    $version = phpversion();
    $majorProblems[] = <<<EOF
        Estás ejecutando la versión "<strong>$version</strong>" de PHP, pero
        Symfony necesita cuando menos PHP "<strong>5.3.2</strong>" para correr. Antes de usar Symfony, instala
        PHP "<strong>5.3.2</strong>" o más reciente.
EOF;
}

if (!is_dir(__DIR__.'/../vendor/symfony')) {
    $vendorsAreMissing = true;
    $majorProblems[] = '<strong>CRÍTICO</strong>: Faltan las bibliotecas de proveedores. Instala composer siguiendo las instrucciones de: <a href="http://getcomposer.org/">http://getcomposer.org/</a>. Luego ejecuta
        "<strong>php composer.phar install</strong>" para instalarlas.';
} else {
    $vendorsAreMissing = false;
}

if (!is_writable(__DIR__ . '/../app/cache')) {
    $majorProblems[] = 'Cambia los permisos del directorio "<strong>app/cache/</strong>"
        para que el servidor web pueda escribir ahí.';
}

if (!is_writable(__DIR__ . '/../app/logs')) {
    $majorProblems[] = 'Cambia los permisos del directorio "<strong>app/logs/</strong>"
        para que el servidor web pueda escribir ahí.';
}

// extensiones
if (!class_exists('DomDocument')) {
    $minorProblems[] = 'Instala y activa el módulo <strong>php-xml</strong>.';
}

if (!((function_exists('apc_store') && ini_get('apc.enabled')) || function_exists('eaccelerator_put') && ini_get('eaccelerator.enable') || function_exists('xcache_set'))) {
    $minorProblems[] = 'Instala y activa un <strong>acelerador PHP</strong> como APC (extremadamente recomendable).';
}

if (!(!(function_exists('apc_store') && ini_get('apc.enabled')) || version_compare(phpversion('apc'), '3.0.17', '>='))) {
    $majorProblems[] = 'Actualiza tu extensión <strong>APC</strong> (3.0.17+)';
}

if (!function_exists('mb_strlen')) {
    $minorProblems[] = 'Instala y activa la extensión <strong>mbstring</strong>.';
}

if (!function_exists('iconv')) {
    $minorProblems[] = 'Instala y activa la extensión <strong>iconv</strong>.';
}

if (!function_exists('utf8_decode')) {
    $minorProblems[] = 'Instala y activa la extensión <strong>XML</strong>.';
}

if (!defined('PHP_WINDOWS_VERSION_BUILD') && !function_exists('posix_isatty')) {
    $minorProblems[] = 'Instala y activa la extensión <strong>php_posix</strong> (usada para colorear la salida de la CLI).';
}

if (!class_exists('Locale')) {
    $minorProblems[] = 'Instala y activa la extensión <strong>intl</strong>.';
} else {
    $version = '';

    if (defined('INTL_ICU_VERSION')) {
        $version =  INTL_ICU_VERSION;
    } else {
        $reflector = new \ReflectionExtension('intl');

        ob_start();
        $reflector->info();
        $output = strip_tags(ob_get_clean());

        preg_match('/^ICU version (.*)$/m', $output, $matches);
        $version = $matches[1];
    }

    if (!version_compare($version, '4.0', '>=')) {
        $minorProblems[] = 'Actualiza tu extensión <strong>intl</strong> con una nueva versión de ICU (4+).';
    }
}

if (!function_exists('json_encode')) {
    $majorProblems[] = 'Instala y activa la extensión <strong>json</strong>.';
}

if (!function_exists('session_start')) {
    $majorProblems[] = 'Instala y activa la extensión <strong>session</strong>.';
}

if (!function_exists('ctype_alpha')) {
    $majorProblems[] = 'Instala y activa la extensión <strong>ctype</strong>.';
}

if (!function_exists('token_get_all')) {
    $majorProblems[] = 'Instala y activa la extensión <strong>Tokenizer</strong>.';
}

if (!function_exists('simplexml_import_dom')) {
    $majorProblems[] = 'Instala y activa la extensión <strong>SimpleXML</strong>.';
}

// php.ini
if (!ini_get('date.timezone')) {
    $phpini = true;
    $majorProblems[] = 'Configura la "<strong>date.timezone</strong>" en php.ini<a href="#phpini">*</a> (similar a America/Mexico_City).';
}

if (ini_get('detect_unicode')) {
    $phpini = true;
    $majorProblems[] = 'Configura "<strong>detect_unicode</strong>" a <strong>off</strong> en php.ini<a href="#phpini">*</a>.';
}

$suhosin = ini_get('suhosin.executor.include.whitelist');
if (false !== $suhosin && false === stripos($suhosin, 'phar')) {
    $phpini = true;
    $majorProblems[] = 'Configura la "<strong>suhosin.executor.include.whitelist</strong>" a "<strong>phar'.($suhosin?' '.$suhosin:'').'</strong>" en php.ini<a href="#phpini">*</a>.';
}

if (ini_get('short_open_tag')) {
    $phpini = true;
    $minorProblems[] = 'Configura <strong>short_open_tag</strong> a <strong>off</strong> en php.ini<a href="#phpini">*</a>.';
}

if (ini_get('magic_quotes_gpc')) {
    $phpini = true;
    $minorProblems[] = 'Configura <strong>magic_quotes_gpc</strong> a <strong>off</strong> en php.ini<a href="#phpini">*</a>.';
}

if (ini_get('register_globals')) {
    $phpini = true;
    $minorProblems[] = 'Configura <strong>register_globals</strong> a <strong>off</strong> en php.ini<a href="#phpini">*</a>.';
}

if (ini_get('session.auto_start')) {
    $phpini = true;
    $minorProblems[] = 'Configura <strong>session.auto_start</strong> a <strong>off</strong> en php.ini<a href="#phpini">*</a>.';
}

?>
<!DOCTYPE html>
<html lang="en">
    <head>
        <meta charset="UTF-8" />
        <?php if (!$vendorsAreMissing): ?>
        <link rel="stylesheet" href="bundles/sensiodistribution/webconfigurator/css/install.css" />
        <?php endif; ?>
        <title>Configurando Symfony</title>
    </head>
    <body>
        <div id="symfony-wrapper">
            <div id="symfony-content">
                <div class="symfony-blocks-install">
                    <?php if (!$vendorsAreMissing): ?>
                    <div class="symfony-block-logo">
                        <img src="bundles/sensiodistribution/webconfigurator/images/logo-big.gif" alt="Logo de Symfony" />
                    </div>
                    <?php endif; ?>

                    <div class="symfony-block-content">
                        <h1>Welcome!</h1>
                        <p>Bienvenido a tu nuevo proyecto Symfony.</p>
                        <p>
                            Este programa te guiará a través de la configuración básica de tu proyecto. 
                            También puedes hacer los mismo editando el archivo ‘<strong>app/config/parameters.yml</strong>’ directamente.
                        </p>

                        <?php if (count($majorProblems)): ?>
                            <h2><?php echo count($majorProblems) ?> Problemas importantes</h2>
                            <p>Se han detectado importantes problemas y los <strong>debes</strong> solucionar antes de continuar:</p>
                            <ol>
                                <?php foreach ($majorProblems as $problem): ?>
                                    <li><?php echo $problem ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if (count($minorProblems)): ?>
                            <h2>Recomendaciones</h2>
                            <p>
                                <?php if ($majorProblems): ?>Adicionalmente, para<?php else: ?>Para<?php endif; ?> mejora tu experiencia con Symfony, 
                                es recomendable que corrijas lo siguiente:
                            </p>
                            <ol>
                                <?php foreach ($minorProblems as $problem): ?>
                                    <li><?php echo $problem ?></li>
                                <?php endforeach; ?>
                            </ol>
                        <?php endif; ?>

                        <?php if ($phpini): ?>
                            <p id="phpini">*
                                <?php if (get_cfg_var('cfg_file_path')): ?>
                                    Los cambios al archivo <strong>php.ini</strong> los debes hacer en "<strong><?php echo get_cfg_var('cfg_file_path') ?></strong>".
                                <?php else: ?>
                                    Para cambiar las opciones, crea un "<strong>php.ini</strong>".
                                <?php endif; ?>
                            </p>
                        <?php endif; ?>

                        <ul class="symfony-install-continue">
                            <?php if (!count($majorProblems)): ?>
                                <li><a href="app_dev.php/_configurator/">Configura en línea tu aplicación Symfony</a></li>
                                <li><a href="app_dev.php/">Pospón la configuración y llévame a la página de bienvenida</a></li>
                            <?php endif; ?>
                            <li><a href="config.php">Vuelve a probar</a></li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
        <div class="version">Edición estándar de Symfony</div>
    </body>
</html>
