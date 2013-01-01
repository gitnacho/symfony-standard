<?php

/*
 * Este archivo es parte del paquete Symfony.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * Para información completa sobre los derechos de autor y licencia, por
 * favor ve el archivo LICENSE adjunto a este código fuente.
 */

/*
 * Usuarios de PHP 5.2 deben poder ejecutar la comprobación de requisitos.
 * Esto es por lo que el archivo y todas las clases tienen que ser compatibles
 * con PHP 5.2+ (p.e. no usando espacios de nombres y cierres).
 *
 * ************** PRECAUCIÓN **************
 *
 * NO EDITES ESTE ARCHIVO cuando sea sobrescrito por composer como parte
 * del proceso de instalación/actualización. El archivo original reside en el
 * SensioDistributionBundle.
 *
 * ************** PRECAUCIÓN **************
 */

/**
 * Representa un solo requisito PHP, p.e. una extensión instalada.
 * Este puede ser obligatorio o una recomendación opcional.
 * Hay una subclase especial, llamada PhpIniRequirement, para
 *     examinar un archivo de configuración php.ini.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class Requirement
{
    private $fulfilled;
    private $testMessage;
    private $helpText;
    private $helpHtml;
    private $optional;

    /**
     * Constructor que inicia el requisito.
     *
     * @param Boolean      $fulfilled     Si se cumple el requisito
     * @param string       $testMessage   El mensaje para probar el requisito
     * @param string       $helpHtml      El texto de ayuda en formato HTML
     *                                    para resolver el problema
     * @param string|null  $helpText      El texto de ayuda (cuando es null,
     *                                       se inferirá desde $helpHtml,
     *                                       Es decir, será despojado de
     *                                       las etiquetas HTML)
     * @param Boolean      $optional      Si este requisito solo es una
     *                                       recomendación opcional no
     *                                       obligatoria
     */
    public function __construct($fulfilled, $testMessage, $helpHtml, $helpText = null, $optional = false)
    {
        $this->fulfilled = (Boolean) $fulfilled;
        $this->testMessage = (string) $testMessage;
        $this->helpHtml = (string) $helpHtml;
        $this->helpText = null === $helpText ? strip_tags($this->helpHtml) : (string) $helpText;
        $this->optional = (Boolean) $optional;
    }

    /**
     * Regresa si el requisito se cumple.
     *
     * @return Boolean true si se cumple, de lo contrario false
     */
    public function isFulfilled()
    {
        return $this->fulfilled;
    }

    /**
     * Devuelve el mensaje para probar el requisito.
     *
     * @return string El mensaje de prueba
     */
    public function getTestMessage()
    {
        return $this->testMessage;
    }

    /**
     * Devuelve el texto de ayuda para resolver el problema
     *
     * @return string El texto de ayuda
     */
    public function getHelpText()
    {
        return $this->helpText;
    }

    /**
     * Devuelve el texto de ayuda en formato HTML.
     *
     * @return string La ayuda HTML
     */
    public function getHelpHtml()
    {
        return $this->helpHtml;
    }

    /**
     * Regresa si este únicamente es un requisito opcional y no uno obligatorio.
     *
     * @return Boolean true si es opcional, false si es obligatorio
     */
    public function isOptional()
    {
        return $this->optional;
    }
}

/**
 * Representa un requisito PHP en forma de una configuración php.ini.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class PhpIniRequirement extends Requirement
{
    /**
     * Constructor que inicia el requisito.
     *
     * @param string            $cfgName            El nombre de la configuración
     *                                       usado por ini_get()
     * @param Boolean|callback  $evaluation         Ya sea un Boolean indicando
     *                                       si la configuración debe
     *                                       evaluar a true o false, o
     *                                       una función retrollamada
     *                                       que recibe el valor de
     *                                       configuración como
     *                                       parámetro para determinar
     *                                       el cumplimiento del
     *                                       requisito
     * @param Boolean           $approveCfgAbsence  Si es true el requisito se
     *                                              debe cumplir incluso si
     *                                              la opción de configuración
     *                                              no existe, es decir,
     *                                              ini_get() devuelve false.
                                                    Esto es útil para
     *                                              configuraciones abandonadas
     *                                              en posteriores versiones
     *                                              de PHP o configuración de
     *                                              una extensión opcional,
     *                                              tal como Suhosin.
                                                    Ejemplo: Requieres que una
     *                                      configuración sea true, pero más
     *                                              adelante PHP elimina
     *                                              esta configuración y por
     *                                              omisión internamente es
     *                                              true.
     * @param string|null       $testMessage        El mensaje para probar requisitos (cuándo
                                             es null y $evaluation es un Booleano un
                                             mensaje predefinido es derivado)
     * @param string            $helpHtml           El texto de ayuda en formato
     *                                              HTML para resolver el
     *                                              problema (cuando es null
     *                                              y $evaluation es un valor
     *                                              booleano del cual se deriva
     *                                              la ayuda predeterminada)
     * @param string|null       $helpText           El texto de ayuda (cuando es
     *                                              null, se inferirá desde
     *                                              $helpHtml, es decir,
     *                                              será despojado de las
     *                                              etiquetas HTML)
     * @param Boolean           $optional           Si este sólo es una
     *                                              recomendación opcional no
     *                                              un requisito obligatorio
     */
    public function __construct($cfgName, $evaluation, $approveCfgAbsence = false, $testMessage = null, $helpHtml = null, $helpText = null, $optional = false)
    {
        $cfgValue = ini_get($cfgName);

        if (is_callable($evaluation)) {
            if (null === $testMessage || null === $helpHtml) {
                throw new InvalidArgumentException('Debes suministrar los parámetros testMessage y helpHtml para evaluar una retrollamada');
            }

            $fulfilled = call_user_func($evaluation, $cfgValue);
        } else {
            if (null === $testMessage) {
                $testMessage = sprintf('%s %s estar %s en php.ini',
                    $cfgName,
                    $optional ? 'tiene que' : 'debe',
                    $evaluation ? 'activada' : 'desactivada'
                );
            }

            if (null === $helpHtml) {
                $helpHtml = sprintf('Configura <strong>%s</strong> a <strong>%s</strong> en php.ini<a href="#phpini">*</a>.',
                    $cfgName,
                    $evaluation ? 'on' : 'off'
                );
            }

            $fulfilled = $evaluation == $cfgValue;
        }

        parent::__construct($fulfilled || ($approveCfgAbsence && false === $cfgValue), $testMessage, $helpHtml, $helpText, $optional);
    }
}

/**
 * Una RequirementCollection representa un conjunto de instancias Requirement.
 *
 * @author Tobias Schultze <http://tobion.de>
 */
class RequirementCollection implements IteratorAggregate
{
    private $requirements = array();

    /**
     * Recupera la RequirementCollection actual como un Iterator.
     *
     * @return Traversable Una interfaz Traversable
     */
    public function getIterator()
    {
        return new ArrayIterator($this->requirements);
    }

    /**
     * Agrega un Requisito.
     *
     * @param Requirement $requirement Una instancia de Requirement
     */
    public function add(Requirement $requirement)
    {
        $this->requirements[] = $requirement;
    }

    /**
     * Agrega un requisito obligatorio.
     *
     * @param Boolean      $fulfilled     Si se cumple el requisito
     * @param string       $testMessage   El mensaje para probar el requisito
     * @param string       $helpHtml      El texto de ayuda en formato HTML
     *                                    para resolver el problema
     * @param string|null  $helpText      El texto de ayuda (cuando es null, este 
     *                                    será inferido desde $helpHtml, es
     *                                    decir será despojado de las
     *                                    etiquetas HTML)
     */
    public function addRequirement($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new Requirement($fulfilled, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Agrega una recomendación opcional.
     *
     * @param Boolean      $fulfilled     Si cumple la recomendación
     * @param string       $testMessage   El mensaje para probar la recomendación
     * @param string       $helpHtml      El texto de ayuda en formato HTML
     *                                    para resolver el problema
     * @param string|null  $helpText      El texto de ayuda (cuando es null, este 
     *                                    será inferido desde $helpHtml, es
     *                                    decir será despojado de las
     *                                    etiquetas HTML)
     */
    public function addRecommendation($fulfilled, $testMessage, $helpHtml, $helpText = null)
    {
        $this->add(new Requirement($fulfilled, $testMessage, $helpHtml, $helpText, true));
    }

    /**
     * Agrega un requisito obligatorio en forma de una configuración de php.ini.
     *
     * @param string            $cfgName            El nombre de la configuración
     *                                       usado por ini_get()
     * @param Boolean|callback  $evaluation         Ya sea un Boolean indicando
     *                                       si la configuración debe
     *                                       evaluar a true o false, o
     *                                       una función retrollamada
     *                                       que recibe el valor de
     *                                       configuración como
     *                                       parámetro para determinar
     *                                       el cumplimiento del
     *                                       requisito
     * @param Boolean           $approveCfgAbsence  Si es true el requisito se
     *                                              debe cumplir incluso si
     *                                              la opción de configuración
     *                                              no existe, es decir,
     *                                              ini_get() devuelve false.
                                                    Esto es útil para
     *                                              configuraciones abandonadas
     *                                              en posteriores versiones
     *                                              de PHP o configuración de
     *                                              una extensión opcional,
     *                                              tal como Suhosin.
                                                    Ejemplo: Requieres que una
     *                                      configuración sea true, pero más
     *                                              adelante PHP elimina
     *                                              esta configuración y por
     *                                              omisión internamente es
     *                                              true.
     * @param string            $testMessage        El mensaje para probar el
     *                                              requisito (cuando es null
     *                                              y $evaluation es un valor
     *                                              booleano se deriva un
     *                                              mensaje predeterminado)
     * @param string            $helpHtml           El texto de ayuda en formato
     *                                              HTML para resolver el
     *                                              problema (cuando es null
     *                                              y $evaluation es un valor
     *                                              booleano se deriva la
     *                                              ayuda predeterminada)
     * @param string|null       $helpText           El texto de ayuda (cuando es
     *                                              null, este será inferido
     *                                              desde $helpHtml, es decir
     *                                              será despojado de las
     *                                              etiquetas HTML)
     */
    public function addPhpIniRequirement($cfgName, $evaluation, $approveCfgAbsence = false, $testMessage = null, $helpHtml = null, $helpText = null)
    {
        $this->add(new PhpIniRequirement($cfgName, $evaluation, $approveCfgAbsence, $testMessage, $helpHtml, $helpText, false));
    }

    /**
     * Agrega una recomendación opcional en forma de una configuración de php.ini.
     *
     * @param string            $cfgName            El nombre de la configuración
     *                                       usado por ini_get()
     * @param Boolean|callback  $evaluation         Ya sea un Boolean indicando
     *                                       si la configuración debe
     *                                       evaluar a true o false, o
     *                                       una función retrollamada
     *                                       que recibe el valor de
     *                                       configuración como
     *                                       parámetro para determinar
     *                                       el cumplimiento del
     *                                       requisito
     * @param Boolean           $approveCfgAbsence  Si es true el requisito se
     *                                              debe cumplir incluso si
     *                                              la opción de configuración
     *                                              no existe, es decir,
     *                                              ini_get() devuelve false.
                                                    Esto es útil para
     *                                              configuraciones abandonadas
     *                                              en posteriores versiones
     *                                              de PHP o configuración de
     *                                              una extensión opcional,
     *                                              tal como Suhosin.
                                                    Ejemplo: Requieres que una
     *                                      configuración sea true, pero más
     *                                              adelante PHP elimina
     *                                              esta configuración y por
     *                                              omisión internamente es
     *                                              true.
     * @param string            $testMessage        El mensaje para probar el
     *                                              requisito (cuando es null
     *                                              y $evaluation es un valor
     *                                              booleano se deriva un
     *                                              mensaje predeterminado)
     * @param string            $helpHtml           El texto de ayuda en formato
     *                                              HTML para resolver el
     *                                              problema (cuando es null
     *                                              y $evaluation es un valor
     *                                              booleano se deriva la
     *                                              ayuda predeterminada)
     * @param string|null       $helpText           El texto de ayuda (cuando es
     *                                              null, este será inferido
     *                                              desde $helpHtml, es decir
     *                                              será despojado de las
     *                                              etiquetas HTML)
     */
    public function addPhpIniRecommendation($cfgName, $evaluation, $approveCfgAbsence = false, $testMessage = null, $helpHtml = null, $helpText = null)
    {
        $this->add(new PhpIniRequirement($cfgName, $evaluation, $approveCfgAbsence, $testMessage, $helpHtml, $helpText, true));
    }

    /**
     * Agrega una colección de requisitos al conjunto de requisitos actual.
     *
     * @param RequirementCollection $collection Una instancia de RequirementCollection
     */
    public function addCollection(RequirementCollection $collection)
    {
        $this->requirements = array_merge($this->requirements, $collection->all());
    }

    /**
     * Devuelve ambos, requisitos y recomendaciones.
     *
     * @return array Una matriz de instancias de Requirement
     */
    public function all()
    {
        return $this->requirements;
    }

    /**
     * Devuelve todos los requisitos obligatorios.
     *
     * @return array Una matriz de instancias de Requirement
     */
    public function getRequirements()
    {
        $array = array();
        foreach ($this->requirements as $req) {
            if (!$req->isOptional()) {
                $array[] = $req;
            }
        }

        return $array;
    }

    /**
     * Devuelve los requisitos obligatorios que no se han cumplido.
     *
     * @return array Una matriz de instancias de requisitos
     */
    public function getFailedRequirements()
    {
        $array = array();
        foreach ($this->requirements as $req) {
            if (!$req->isFulfilled() && !$req->isOptional()) {
                $array[] = $req;
            }
        }

        return $array;
    }

    /**
     * Devuelve todas las recomendaciones opcionales.
     *
     * @return array Una matriz de instancias de requisitos
     */
    public function getRecommendations()
    {
        $array = array();
        foreach ($this->requirements as $req) {
            if ($req->isOptional()) {
                $array[] = $req;
            }
        }

        return $array;
    }

    /**
     * Devuelve todas las recomendaciones que no se han cumplido.
     *
     * @return array Matriz de instancias de requisitos
     */
    public function getFailedRecommendations()
    {
        $array = array();
        foreach ($this->requirements as $req) {
            if (!$req->isFulfilled() && $req->isOptional()) {
                $array[] = $req;
            }
        }

        return $array;
    }

    /**
     * Devuelve si una configuración en php.ini no es correcta.
     *
     * @return Boolean php.ini ¿Problema de configuración?
     */
    public function hasPhpIniConfigIssue()
    {
        foreach ($this->requirements as $req) {
            if (!$req->isFulfilled() && $req instanceof PhpIniRequirement) {
                return true;
            }
        }

        return false;
    }

    /**
     * Devuelve la ruta del archivo de configuración de PHP (php.ini).
     *
     * @return string|false La ruta al archivo php.ini
     */
    public function getPhpIniConfigPath()
    {
        return get_cfg_var('cfg_file_path');
    }
}

/**
 * Esta clase especifica todos los requisitos y recomendaciones opcionales que
 * son necesarias para ejecutar la Edición estándar de Symfony.
 *
 * @author Tobias Schultze <http://tobion.de>
 * @author Fabien Potencier <fabien@symfony.com>
 */
class SymfonyRequirements extends RequirementCollection
{
    const REQUIRED_PHP_VERSION = '5.3.3';

    /**
     * Constructor que inicia los requisitos.
     */
    public function __construct()
    {
        /* los siguientes requisitos son obligatorios */

        $installedPhpVersion = phpversion();

        $this->addRequirement(
            version_compare($installedPhpVersion, self::REQUIRED_PHP_VERSION, '>='),
            sprintf('PHP por lo menos debe ser la -v%s (instalada -v%s)', self::REQUIRED_PHP_VERSION, $installedPhpVersion),
            sprintf('Tienes PHP -v"<strong>%s</strong>", pero Symfony por lo menos necesita PHP -v"<strong>%s</strong>".
                Antes de usar Symfony, actualiza tu PHP, preferiblemente a la nueva.',
                $installedPhpVersion, self::REQUIRED_PHP_VERSION),
            sprintf('Instala PHP -v%s+ (tienes instalada la -v%s)', self::REQUIRED_PHP_VERSION, $installedPhpVersion)
        );

        $this->addRequirement(
            version_compare($installedPhpVersion, '5.3.16', '!='),
            'La versión de PHP no debe ser 5.3.16 puesto que Symfony no trabaja apropiadamente con ella,
            'Instala PHP 5.3.17 o más reciente (o degrada PHP a una versión anterior)'
        );

        $this->addRequirement(
            is_dir(__DIR__.'/../vendor/composer'),
            'Debes instalar las bibliotecas de terceros',
            'Faltan bibliotecas de terceros en el directorio Vendor. Instala composer siguiendo la instrucciones de <a href="http://getcomposer.org/">http://getcomposer.org/</a>. ' .
                'Luego ejecuta "<strong>php composer.phar install</strong>" para instalarlas.'
        );

        $baseDir = basename(__DIR__);

        $this->addRequirement(
            is_writable(__DIR__.'/cache'),
            "El directorio $baseDir/cache/ debe ser modificable",
            "Cambia los permisos del directorio \"<strong>$baseDir/cache/</strong>\" para que el servidor web pueda escribir ahí."
        );

        $this->addRequirement(
            is_writable(__DIR__.'/logs'),
            "El directorio $baseDir/logs/ debe ser modificable",
            "Cambia los permisos del directorio \"<strong>$baseDir/logs/</strong>\" para que el servidor web puede escribir ahí."
        );

        $this->addPhpIniRequirement(
            'date.timezone', true, false,
            'Debes configurar la directiva date.timezone',
            'Ajusta "<strong>date.timezone</strong>" en php.ini<a href="#phpini">*</a> (como America/Mexico_City).'
        );

        if (version_compare($installedPhpVersion, self::REQUIRED_PHP_VERSION, '>=')) {
            $this->addRequirement(
                (in_array(date_default_timezone_get(), DateTimeZone::listIdentifiers())),
                sprintf('La zona horaria configurada "%s" debe ser compatible con tu instalación de PHP', date_default_timezone_get()),
                'Tu zona horaria predefinida no es compatible con PHP. Revisa si tienes algún error en tu archivo <strong>php.ini</strong> y échale un vistazo a la lista de zonas horarias incompatibles en <a href="http://php.net/manual/en/timezones.others.php">http://php.net/manual/en/timezones.others.php</a>.'
            );
        }

        $this->addRequirement(
            function_exists('json_encode'),
            'json_encode() debe estar disponible',
            'Instala y activa la ext. <strong>JSON</strong>.'
        );

        $this->addRequirement(
            function_exists('session_start'),
            'session_start() debe estar disponible',
            'Instala y activa la ext. <strong>session</strong>.'
        );

        $this->addRequirement(
            function_exists('ctype_alpha'),
            'ctype_alpha() debe estar disponible',
            'Instala y activa la ext. <strong>ctype</strong>.'
        );

        $this->addRequirement(
            function_exists('token_get_all'),
            'token_get_all() debe estar disponible',
            'Instala y activa la ext. <strong>Tokenizer</strong>.'
        );

        $this->addRequirement(
            function_exists('simplexml_import_dom'),
            'simplexml_import_dom() debe estar disponible',
            'Instala y activa la ext. <strong>SimpleXML</strong>.'
        );

        if (function_exists('apc_store') && ini_get('apc.enabled')) {
            $this->addRequirement(
                version_compare(phpversion('apc'), '3.0.17', '>='),
                'La versi\ón de APC por lo menos debe ser la 3.0.17',
                'Actualiza tu extensi\ón <strong>APC</strong> (3.0.17+).'
            );
        }

        $this->addPhpIniRequirement('detect_unicode', false);

        if (extension_loaded('suhosin')) {
            $this->addPhpIniRequirement(
                'suhosin.executor.include.whitelist',
                create_function('$cfgValue', 'return false !== stripos($cfgValue, "phar");'),
                false,
                'debes configurar correctamente suhosin.executor.include.whitelist en php.ini',
                'Agrega "<strong>phar</strong>" a <strong>suhosin.executor.include.whitelist</strong> en php.ini<a href="#phpini">*</a>.'
            );
        }

        if (extension_loaded('xdebug')) {
            $this->addPhpIniRequirement(
                'xdebug.show_exception_trace', false, true
            );

            $this->addPhpIniRequirement(
                'xdebug.scream', false, true
            );
        }

        $pcreVersion = defined('PCRE_VERSION') ? (float) PCRE_VERSION : null;

        $this->addRequirement(
            null !== $pcreVersion,
            'PCRE extension must be available',
            'Install the <strong>PCRE</strong> extension (version 8.0+).'
        );

        /* siguen las recomendaciones opcionales */

        $this->addRecommendation(
            file_get_contents(__FILE__) === file_get_contents(__DIR__.'/../vendor/sensio/distribution-bundle/Sensio/Bundle/DistributionBundle/Resources/skeleton/app/SymfonyRequirements.php'),
            'Requirements file should be up-to-date',
            'Your requirements file is outdated. Run composer install and re-check your configuration.'
        );

        $this->addRecommendation(
            version_compare($installedPhpVersion, '5.3.4', '>='),
            'You should use at least PHP 5.3.4 due to PHP bug #52083 in earlier versions',
            'Your project might malfunction randomly due to PHP bug #52083 ("Notice: Trying to get property of non-object"). Instala PHP 5.3.4 o más reciente.'
        );

        $this->addRecommendation(
            version_compare($installedPhpVersion, '5.3.8', '>='),
            'When using annotations you should have at least PHP 5.3.8 due to PHP bug #55156',
            'Install PHP 5.3.8 or newer if your project uses annotations.'
        );

        $this->addRecommendation(
            version_compare($installedPhpVersion, '5.4.0', '!='),
            'You should not use PHP 5.4.0 due to the PHP bug #61453',
            'Your project might not work properly due to the PHP bug #61453 ("Cannot dump definitions which have method calls"). Instala PHP 5.4.1 o más reciente.'
        );

        if (null !== $pcreVersion) {
            $this->addRecommendation(
                $pcreVersion >= 8.0,
                sprintf('PCRE extension should be at least version 8.0 (%s installed)', $pcreVersion),
                '<strong>PCRE 8.0+</strong> is preconfigured in PHP since 5.3.2 but you are using an outdated version of it. Symfony probably works anyway but it is recommended to upgrade your PCRE extension.'
            );
        }

        $this->addRecommendation(
            class_exists('DomDocument'),
            'Debes instalar PHP-XML',
            'Instala y activa <strong>PHP-XML</strong>.'
        );

        $this->addRecommendation(
            function_exists('mb_strlen'),
            'mb_strlen() debe estar disponible',
            'Instala y activa la ext. <strong>mbstring</strong>.'
        );

        $this->addRecommendation(
            function_exists('iconv'),
            'iconv() debe estar disponible',
            'Instala y activa la ext. <strong>iconv</strong>.'
        );

        $this->addRecommendation(
            function_exists('utf8_decode'),
            'utf8_decode() debe estar disponible',
            'Instala y activa la ext. <strong>XML</strong>.'
        );

        if (!defined('PHP_WINDOWS_VERSION_BUILD')) {
            $this->addRecommendation(
                function_exists('posix_isatty'),
                'posix_isatty() debe estar disponible',
                'Instala y activa la ext. <strong>php_posix</strong> (usada para colorear la salida en la CLI).'
            );
        }

        $this->addRecommendation(
            class_exists('Locale'),
            'La ext. intl debe estar disponible',
            'Instala y activa la ext. <strong>intl</strong> (usada por los validadores).'
        );

        if (class_exists('Collator')) {
            $this->addRecommendation(
                null !== new Collator('fr_FR'),
                'debes configurar correctamente la ext. intl',
                'La ext. intl no se comporta adecuadamente. Este problema normalmente es de compilaciones de PHP 5.3.X x64 WIN.'
            );
        }

        if (class_exists('Locale')) {
            if (defined('INTL_ICU_VERSION')) {
                $version = INTL_ICU_VERSION;
            } else {
                $reflector = new ReflectionExtension('intl');

                ob_start();
                $reflector->info();
                $output = strip_tags(ob_get_clean());

                preg_match('/^ICU version +(?:=> )?(.*)$/m', $output, $matches);
                $version = $matches[1];
            }

            $this->addRecommendation(
                version_compare($version, '4.0', '>='),
                'intl ICU cuando menos debe ser la -v4+',
                'Actualiza tu ext. <strong>intl</strong> con una nueva ICU (4+).'
            );
        }

        $accelerator =
            (function_exists('apc_store') && ini_get('apc.enabled'))
            ||
            function_exists('eaccelerator_put') && ini_get('eaccelerator.enable')
            ||
            function_exists('xcache_set')
        ;

        $this->addRecommendation(
            $accelerator,
            'Debes tener instalado un acelerador PHP',
            'Instala y activa un <strong>acelerador PHP</strong> como APC (sumamente recomendable).'
        );

        $this->addPhpIniRecommendation('short_open_tag', false);

        $this->addPhpIniRecommendation('magic_quotes_gpc', false, true);

        $this->addPhpIniRecommendation('register_globals', false, true);

        $this->addPhpIniRecommendation('session.auto_start', false);

        $this->addRecommendation(
            class_exists('PDO'),
            'PDO debe estar instalado',
            'Instala <strong>PDO</strong> (obligatorio para Doctrine).'
        );

        if (class_exists('PDO')) {
            $drivers = PDO::getAvailableDrivers();
            $this->addRecommendation(
                count($drivers),
                sprintf('PDO necesita tener algunos controladores instalados (disponibles actualmente: %s)', count($drivers) ? implode(', ', $drivers) : 'none'),
                'Instala los <strong>controladores PDO</strong> (obligatorio para Doctrine).'
            );
        }
    }
}
