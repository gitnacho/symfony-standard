<?php

/*
 * Este archivo es parte del paquete Symfony.
 *
 * (c) Fabien Potencier <fabien@symfony.com>
 *
 * Para información completa sobre los derechos de autor y licencia, por
 * favor ve el archivo LICENSE adjunto a este código fuente.
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
     *                                              usado por ini_get()
     * @param Boolean|callback  $evaluation         Ya sea un Boolean indicando
     *                                              si la configuración debe
     *                                              evaluar a true o false, o
     *                                              una función retrollamada
     *                                              que recibe el valor de
     *                                              configuración como
     *                                              parámetro para determinar
     *                                              el cumplimiento del
     *                                              requisito
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
     *                                              configuración sea true,
     *                                              pero más adelante PHP elimina
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
     *                                              null,se inferirá desde
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
 * Usuarios de PHP 5.2 deben poder ejecutar la comprobación de requisitos.
 * Esto es porque la clase debe ser compatible con PHP 5.2
 * (p.e. no usando espacios de nombres y cierres).
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
     *                                              usado por ini_get()
     * @param Boolean|callback  $evaluation         Ya sea un Boolean indicando
     *                                              si la configuración debe
     *                                              evaluar a true o false, o
     *                                              una función retrollamada
     *                                              que recibe el valor de
     *                                              configuración como
     *                                              parámetro para determinar
     *                                              el cumplimiento del
     *                                              requisito
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
     *                                              configuración sea true,
     *                                              pero más adelante PHP elimina
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
     *                                              usado por ini_get()
     * @param Boolean|callback  $evaluation         Ya sea un Boolean indicando
     *                                              si la configuración debe
     *                                              evaluar a true o false, o
     *                                              una función retrollamada
     *                                              que recibe el valor de
     *                                              configuración como
     *                                              parámetro para determinar
     *                                              el cumplimiento del
     *                                              requisito
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
     *                                              configuración sea true,
     *                                              pero más adelante PHP elimina
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
 */
class SymfonyRequirements extends RequirementCollection
{
    const REQUIRED_PHP_VERSION = '5.3.2';

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
            is_dir(__DIR__.'/../vendor/symfony'),
            'Debes instalar las bibliotecas de proveedores',
            'Faltan las bibliotecas de proveedores. Instala composer siguiendo la instrucciones de <a href="http://getcomposer.org/">http://getcomposer.org/</a>. ' .
                'Luego ejecuta "<strong>php composer.phar install</strong>" para instalarlas.'
        );

        $this->addRequirement(
            is_writable(__DIR__.'/../app/cache'),
            'El directorio app/cache/ debe contar con el privilegio de escritura',
            'Cambia los permisos del directorio "<strong>app/cache/</strong>" para que el servidor web pueda escribir ahí.'
        );

        $this->addRequirement(
            is_writable(__DIR__.'/../app/logs'),
            'El directorio app/logs/ debe contar con el privilegio de escritura',
            'Cambia los permisos del directorio "<strong>app/logs/</strong>" para que el servidor web pueda escribir ahí.'
        );

        $this->addPhpIniRequirement(
            'date.timezone', true, false,
            'Debes configurar la date.timezone',
            'Ajusta "<strong>date.timezone</strong>" en php.ini<a href="#phpini">*</a> (como America/Mexico_City).'
        );

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

        $this->addRequirement(
            !(function_exists('apc_store') && ini_get('apc.enabled')) || version_compare(phpversion('apc'), '3.0.17', '>='),
            'APC cuando menos debe ser la .v3.0.17',
            'Actualiza tu ext. <strong>APC</strong> a (3.0.17+)'
        );

        $this->addPhpIniRequirement('detect_unicode', false);

        $this->addPhpIniRequirement(
            'suhosin.executor.include.whitelist',
            create_function('$cfgValue', 'return false !== stripos($cfgValue, "phar");'),
            true,
            'debes configurar correctamente suhosin.executor.include.whitelist en php.ini',
            'Agrega "<strong>phar</strong>" a <strong>suhosin.executor.include.whitelist</strong> en php.ini<a href="#phpini">*</a>.'
        );

        $pcreVersion = defined('PCRE_VERSION') ? (float) PCRE_VERSION : null;

        $this->addRequirement(
            null !== $pcreVersion && $pcreVersion > 8.0,
            sprintf('La ext. PCRE debe estar disponible y por lo menos debe ser la 8.0 (%s instalada)', $pcreVersion ? $pcreVersion : 'not'),
            'Actualiza tu ext. <strong>PCRE</strong> (8.0+)'
        );

        /* siguen las recomendaciones opcionales */

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
