<?php

if (!$iniPath = get_cfg_var('cfg_file_path')) {
    $iniPath = 'CUIDADO: no se usa un archivo php.ini';
}

echo "***************************************\n";
echo "*                                     *\n";
echo "*  Comprobando requisitos de Symfony  *\n";
echo "*                                     *\n";
echo "***************************************\n\n";
echo sprintf("php.ini usado por PHP: %s\n\n", $iniPath);

echo "** CUIDADO **\n";
echo "*  La CLI de PHP puede usar un archivo php.ini\n";
echo "*  diferente al usado por tu servidor web.\n";
if ('\\' == DIRECTORY_SEPARATOR) {
    echo "*  (especialmente en plataformas Windows)\n";
}
echo "*  Si este es el caso, por favor lanza esta\n";
echo "*  utilidad desde tu servidor web.\n";
echo "** CUIDADO **\n";

// obligatorios
echo_title("Requisitos obligatorios");
check(version_compare(phpversion(), '5.3.2', '>='), sprintf('Comprobando que PHP cuando menos sea la v. 5.3.2 (%s instalada)', phpversion()), 'Instala PHP 5.3.2 o más reciente (actualmente tienes la '.phpversion(), true);
check(is_dir(__DIR__.'/../vendor/symfony'), 'Comprobando las bibliotecas de proveedores instaladas', 'Faltan las bibliotecas de proveedores; Instala composer siguiendo las instrucciones desde http://getcomposer.org/ y luego ejecuta "php composer.phar install" para instalarlas', true);
check(ini_get('date.timezone'), 'Comprobando se haya ajustado la "date.timezone"', 'Ajusta la "date.timezone" en php.ini (como America/Mexico_City)', true);
check(is_writable(__DIR__.'/../app/cache'), sprintf('Comprobando que se puede escribir en el directorio app/cache/'), 'Cambia los permisos del directorio app/cache/ para que el servidor web pueda escribir en ese lugar', true);
check(is_writable(__DIR__.'/../app/logs'), sprintf('Comprobando que se puede escribir en el directorio app/logs/'), 'Cambia los permisos del directorio app/logs/ para que el servidor web pueda escribir en ese lugar', true);
check(function_exists('json_encode'), 'Comprobando disponibilidad de json_encode()', 'Instala y activa la ext. json', true);
check(function_exists('session_start'), 'Comprobando disponibilidad de session_start()', 'Instala y activa la ext. session', true);
check(function_exists('ctype_alpha'), 'Comprobando disponibilidad de ctype_alpha()', 'Instala y activa la ext. ctype', true);
check(function_exists('token_get_all'), 'Comprobando disponibilidad de token_get_all()', 'Instala y activa la ext. tokenizer', true);
check(function_exists('simplexml_import_dom'), 'Comprobando disponibilidad de simplexml_import_dom()', 'Instala y activa la ext. simplexml', true);
check(!(function_exists('apc_store') && ini_get('apc.enabled')) || version_compare(phpversion('apc'), '3.0.17', '>='), 'Comprobando que APC cuando menos sea la v. 3.0.17', 'Actualiza tu ext. APC (3.0.17+)', true);
check(!ini_get('detect_unicode'), 'Comprobando que php.ini tenga detect_unicode configurado en off', 'Configura detect_unicode a off en php.ini', true);
$suhosin = ini_get('suhosin.executor.include.whitelist');
check(false === $suhosin || false !== stripos($suhosin, 'phar'), 'Comprobando que php.ini tenga correctamente configurado suhosin.executor.include.whitelist', 'Configura suhosin.executor.include.whitelist a "phar'.($suhosin? ' '.$suhosin:'').'"  in php.ini', true);

// Advertencias
echo_title("Comprobaciones opcionales");
check(class_exists('DomDocument'), 'Comprobando disponibilidad del módulo PHP-XML', 'Instala y activa el módulo php-xml', false);
check(function_exists('mb_strlen'), 'Comprobando disponibilidad de la func. mb_strlen()', 'Instala y activa la ext. mbstring', false);
check(function_exists('iconv'), 'Comprobando disponibilidad de la func. iconv()', 'Instala y activa la ext. iconv', false);
check(function_exists('utf8_decode'), 'Comprobando disponibilidad de utf8_decode()', 'Instala y activa la ext. XML', false);
if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
    check(function_exists('posix_isatty'), 'Comprobando disponibilidad de posix_isatty()', 'Instala y activa la ext. php_posix (usada para colorear la salida en la CLI)', false);
}
check(class_exists('Locale'), 'Comprobando disponibilidad de la ext. intl', 'Instala y activa la ext. intl (usada por los validadores)', false);
if (class_exists('Locale')) {
    $version = '';

    if (defined('INTL_ICU_VERSION')) {
        $version =  INTL_ICU_VERSION;
    } else {
        $reflector = new \ReflectionExtension('intl');

        ob_start();
        $reflector->info();
        $output = strip_tags(ob_get_clean());

        preg_match('/^ICU version +(?:=> )?(.*)$/m', $output, $matches);
        $version = $matches[1];
    }

    check(version_compare($version, '4.0', '>='), 'Comprobando que la ICU intl cuando menos sea la v. 4+', 'Actualiza tu ext. intl con una nueva ICU (v. 4+)', false);
}

$accelerator =
    (function_exists('apc_store') && ini_get('apc.enabled'))
    ||
    function_exists('eaccelerator_put') && ini_get('eaccelerator.enable')
    ||
    function_exists('xcache_set')
;
check($accelerator, 'Comprobando disponibilidad de un acelerador para PHP', 'Instala un acelerador de PHP como APC (extremadamente recomendable)', false);

check(!ini_get('short_open_tag'), 'Comprobando que php.ini tenga short_open_tag configurado en off', 'Configura short_open_tag a off en php.ini', false);
check(!ini_get('magic_quotes_gpc'), 'Comprobando que php.ini tenga magic_quotes_gpc configurado en off', 'Configura magic_quotes_gpc a off en php.ini', false);
check(!ini_get('register_globals'), 'Comprobando que php.ini tenga register_globals configuradas en off', 'Configura register_globals a off en php.ini', false);
check(!ini_get('session.auto_start'), 'Comprobando que php.ini tenga session.auto_start configurada en off', 'Configura session.auto_start a off en php.ini', false);

echo_title("Comprobaciones opcionales (Doctrine)");

check(class_exists('PDO'), 'Comprobando disponibilidad de PDO', 'Instala PDO (obligatorio para Doctrine)', false);
if (class_exists('PDO')) {
    $drivers = PDO::getAvailableDrivers();
    check(count($drivers), 'Comprobando que PDO tenga instalados algunos controladores: '.implode(', ', $drivers), 'Instala controladores de PDO (obligatorio para Doctrine)');
}

/**
 * Comprueba una configuración.
 */
function check($boolean, $message, $help = '', $fatal = false)
{
    echo $boolean ? "  BIEN      " : sprintf("\n\n[[%s]] ", $fatal ? ' ERROR ' : 'CUIDADO');
    echo sprintf("$message%s\n", $boolean ? '' : ': FALLA');

    if (!$boolean) {
        echo "            *** $help ***\n";
        if ($fatal) {
            exit("Debes solucionar estos problemas antes de continuar.\n");
        }
    }
}

function echo_title($title)
{
    echo "\n** $title **\n\n";
}
