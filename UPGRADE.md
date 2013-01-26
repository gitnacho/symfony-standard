Actualizando la edición estándar de *Symfony*
=============================================

De *Symfony* 2.0 a *Symfony* 2.1
--------------------------------

### Dependencias del proyecto

Debido a que las dependencias en los proyectos *Symfony 2.1*, son gestionadas por [composer](http://getcomposer.org/):

* Puedes eliminar el guión `bin/vendors` puesto que ahora `composer.phar` hace todo el trabajo (es recomendable que lo instales globalmente en tu máquina).

* Es necesario sustituir el archivo `deps` con un `composer.json`.

* El archivo `composer.lock` es el equivalente del `deps.lock` generado y este automáticamente lo crea `Composer`.

Descarga los archivos
[`composer.json`](https://raw.github.com/symfony/symfony-standard/2.1/composer.json) y [`composer.lock`](https://raw.github.com/symfony/symfony-standard/2.1/composer.lock) predefinidos para *Symfony 2.1* y ponlos en el directorio principal de tu proyecto. Si has personalizado tu archivo `deps`, mueve las dependencias añadidas al archivo `composer.json` (muchos paquetes y bibliotecas *PHP* ya están disponibles como paquetes de `Composer` -- búscalos en [Packagist](http://packagist.org/)).

Elimina tu directorio `vendor` actual.

Finalmente, ejecuta `Composer`:

    $ composer.phar install

Nota: Debes completar los pasos de actualización de abajo para que `composer` pueda generar los archivos `autoload` exitosamente.

### `app/autoload.php`

El `autoload.php` predeterminado se lee como sigue (se ha simplificado mucho para que las bibliotecas cargadas automáticamente y los paquetes declarados en tu archivoc `composer.json` sean gestionados automáticamente por el autocargador de `Composer`):

    <?php

    use Doctrine\Common\Annotations\AnnotationRegistry;

    $loader = include __DIR__.'/../vendor/autoload.php';

    // intl
    if (!function_exists('intl_get_error_code')) {
        require_once __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs/functions.php';

        $loader->add('', __DIR__.'/../vendor/symfony/symfony/src/Symfony/Component/Locale/Resources/stubs');
    }

    AnnotationRegistry::registerLoader(array($loader, 'loadClass'));

    return $loader;

### `app/config/config.yml`

La opción `framework.charset` se tiene que eliminar. Si no estás usando `UTF-8` en tu aplicación, en su lugar sustituye el método `getCharset()` en tu
clase `AppKernel`:

    class AppKernel extends Kernel
    {
        public function getCharset()
        {
            return 'ISO-8859-1';
        }

        // ...
    }

Podrías querer añadir el nuevo parámetro `strict_requirements` a `framework.router`
(evita errores fatales en el entorno de producción cuándo no se puede generar un enlace):

    framework:
        router:
            strict_requirements: %kernel.debug%

Incluso, en producción puedes deshabilitar la comprobación de requisitos con `null` porque deberías saber que los parámetros para generar la *URL* siempre pasan los requisitos, p. ej. validándolos por adelantado. Esto además mejora el rendimiento. Consulta [config_prod.yml](https://github.com/symfony/symfony-standard/blob/master/app/config/config_prod.yml).

El parámetro `default_locale` predefinido ahora es una opción de la configuración del `framework` principal (este, en la versión 2.0 estaba bajo `framework.session`):

    framework:
        default_locale: "%locale%"

La opción `auto_start` bajo `framework.session` se tiene que eliminar cuando ya no
se utiliza (ahora, la sesión siempre se inicia bajo demanda). Si `auto_start` era la única opción bajo la clave `framework.session`, no la elimines por completo, sino que en su lugar pon su valor a `~` (en *YAML* `~` significa `null`):

    framework:
        session: ~

La cabecera `trust_proxy_headers` se añadió en el archivo de configuración predefinido (puesto que esta se debe poner a `true` cuándo instalas tu aplicación detrás de un delegado inverso):

    framework:
        trust_proxy_headers: false

Una entrada `bundles` vacía fue añadida a la configuración de `assetic`:

    assetic:
        bundles: []

La configuración predefinida del `swiftmailer` ahora tiene la opción `spool` configurada al tipo `memory` para diferir el envío del correo electrónico después de que se haya enviado la respuesta al
usuario (recomendado para mejorar la experiencia del usuario en cuanto al rendimiento):

    swiftmailer:
        spool: { type: memory }

La configuración del `jms_security_extra` se movió al archivo de configuración `security.yml`.

### `app/config/config_dev.yml`

Se añadió un ejemplo de cómo enviar todos los correos electrónicos a una única dirección:

    #swiftmailer:
    #    delivery_address: yo@ejemplo.com

### `app/config/config_test.yml`

La opción `storage_id` se tiene que cambiar a `session.storage.mock_file`:

    framework:
        session:
            storage_id: session.storage.mock_file

### `app/config/parameters.ini`

El archivo se convirtió a un archivo *YAML* el cual se lee como sigue:

    parameters:
        database_driver:   pdo_mysql
        database_host:     localhost
        database_port:     ~
        database_name:     symfony
        database_user:     root
        database_password: ~

        mailer_transport:  smtp
        mailer_host:       localhost
        mailer_user:       ~
        mailer_password:   ~

        locale:            en
        secret:            EsteSegmentoNoEsTanSecretoCambialo

Ten en cuenta que si conviertes tu archivo de parámetros a *YAML*, también tienes que cambiar
su referencia en `app/config/config.yml`.

### `app/config/routing_dev.yml`

Se eliminó la entrada `_assetic`:

    #_assetic:
    #    resource: .
    #    type:     assetic

### `app/config/security.yml`

Bajo `security.access_control`, cambió la regla predefinida para las rutas internas:

    security:
        access_control:
            #- { path: ^/_internal/secure, roles: IS_AUTHENTICATED_ANONYMOUSLY, ip: 127.0.0.1 }

Bajo `security.providers`, el ejemplo `in_memory` se actualizó a lo siguiente:

    security:
        providers:
                in_memory:
                    memory:
                        users:
                            usuario:  { password: paseusuario, roles: [ 'ROLE_USER' ] }
                            admin: { password: paseadmin, roles: [ 'ROLE_ADMIN' ] }

### `app/AppKernel.php`

Los siguientes paquetes se han añadido a la lista de paquetes registrados por omisión:

    new JMS\AopBundle\JMSAopBundle(),
    new JMS\DiExtraBundle\JMSDiExtraBundle($this),

También tienes que renombrar el DoctrineBundle de:

    new Symfony\Bundle\DoctrineBundle\DoctrineBundle(),

a:

    new Doctrine\Bundle\DoctrineBundle\DoctrineBundle(),

### `web/app.php`

El archivo `web/app.php` ahora se lee como sigue:

    <?php

    use Symfony\Component\ClassLoader\ApcClassLoader;
    use Symfony\Component\HttpFoundation\Request;

    $loader = require_once __DIR__.'/../app/bootstrap.php.cache';

    // Usa APC para mejorar el rendimiento de la carga automática.
    // Cambia 'sf2' a un único prefijo para impedir conflictos de claves en caché
    // con otras aplicaciones que también utilizan APC.
    /*
    $loader = new ApcClassLoader('sf2', $loader);
    $loader->register(true);
    */

    require_once __DIR__.'/../app/AppKernel.php';
    //require_once __DIR__.'/../app/AppCache.php';

    $kernel = new AppKernel('prod', false);
    $kernel->loadClassCache();
    //$kernel = new AppCache($kernel);
    $request = Request::createFromGlobals();
    $response = $kernel->handle($request);
    $response->send();
    $kernel->terminate($request, $response);

### `web/app_dev.php`

El archivo `web/app_dev.php` ahora se lee como sigue:

    <?php

    use Symfony\Component\HttpFoundation\Request;

    // Si no quieres configurar los permisos de la manera convencional sólo
    // quita el comentario de la siguiente línea PHP, para más información lee
    // http://gitnacho.github.com/symfony-docs-es/book/installation.html#instalando-y-configurando-symfony
    //umask(0000);

    // Esto previene el acceso para depurar los controladores frontales que se
    // despliegan por accidente a los servidores de producción.
    // Siéntete libre de quitarlo, extenderlo, o hacer algo mucho más sofisticado.
    if (isset($_SERVER['HTTP_CLIENT_IP'])
        || isset($_SERVER['HTTP_X_FORWARDED_FOR'])
        || !in_array(@$_SERVER['REMOTE_ADDR'], array(
            '127.0.0.1',
            '::1',
        ))
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
