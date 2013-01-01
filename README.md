Edición estándar de *Symfony* en español
========================================

Bienvenido a la edición estándar de *Symfony* en español — una aplicación *Symfony2* totalmente funcional que puedes utilizar como el esqueleto de tu nueva aplicación con la ligera ventaja de que está traducida al español.

Este documento contiene información sobre cómo descargar y comenzar a utilizar *Symfony*. Para una explicación más detallada, ve el capítulo [Instalación][1] de la documentación de *Symfony*.

1) Instalando la edición estándar
---------------------------------

Cuándo vas a instalar la edición estándar de *Symfony*, tienes las siguientes opciones.

### Usar `composer` (recomendado)

Debido a que *Symfony* usa [composer][2] para gestionar sus dependencias, la manera recomendable de crear un nuevo proyecto es utilizarlo.

Si todavía no tienes `composer`, descárgalo siguiendo las instrucciones en http://getcomposer.org/ o sólo ejecuta la siguiente orden:

    curl -s http://getcomposer.org/installer | php

Luego, usa la orden `create-project` para generar una nueva aplicación *Symfony*:

    php composer.phar create-project symfony/framework-standard-edition ruta/a/instalar

`Composer` instalará *Symfony* y todas sus dependencias bajo el directorio `ruta/a/instalar`.

### Descarga un Archivo

Para probar *Symfony* rápidamente, también puedes descargar un [archivo][3] de la Edición
Estándar y desempaquetarlo en algún lugar bajo tu directorio raíz del servidor de *web*.

Si descargaste un archivo «sin proveedores», también necesitas instalar todas las dependencias necesarias. Descarga `composer` (ve arriba) y ejecuta la siguiente orden:

    php composer.phar install

2) Comprobando la configuración de tu sistema
---------------------------------------------

Antes de empezar a codificar, asegúrate que tu sistema local está configurado correctamente para *Symfony*.

Ejecuta el archivo `check.php` desde la línea de ordenes:

    php app/check.php

Accede al guión `config.php` desde un navegador:

    http://localhost/path/to/symfony/app/web/config.php

Si obtienes algún aviso o recomendación, arréglalo antes de continuar.

3) Explorando la aplicación de demostración
-------------------------------------------

¡Enhorabuena! Ahora estás listo para usar Symfony.

Desde la página `config.php`, haz clic en el enlace «Pospón la configuración y llévame a la página de bienvenida» para cargar tu primer página *Symfony*.

También puedes utilizar un configurador basado en *web* haciendo clic en el enlace a «Configura en línea tu aplicación *Symfony*» de la página `config.php`.

Para ver en acción páginas *Symfony* reales, accede a la siguiente página:

    `web/app_dev.php/demo/hello/Symfony`

4) Empezando con *Symfony*
--------------------------

Esta distribución está diseñada para ser el punto de partida de tus aplicaciones *Symfony*, pero también contiene algún código de muestra con el cual puedes jugar y aprender.

Una gran manera de empezar a aprender *Symfony* es vía la guía de [Inicio rápido][4], la cual te lleva de la mano a través de todas las características básicas de *Symfony2*.

Una vez te sientas a gusto, puedes avanzar y leer el [libro oficial de Symfony2][5].

Un paquete predefinido, `AcmeDemoBundle`, muestra a *Symfony2* en acción. 
Después de jugar con él, lo puedes eliminar siguiendo estos pasos:

  * elimina el directorio `src/Acme`;

  * quita las entradas de enrutado que hacen referencia a `AcmeBundle` en `app/config/routing_dev.yml`;

  * quita el `AcmeBundle` de los paquetes registrados en `app/AppKernel.php`;

  * elimina el directorio `web/bundles/acmedemo`;

  * elimina las entradas de `security.providers`, `security.firewalls.login` y `security.firewalls.secured_area` del archivo `security.yml` o ajusta la configuración de seguridad para cubrir tus necesidades.

¿Qué hay dentro?
----------------

La edición estándar de *Symfony* viene preconfigurada con los siguientes paquetes:

  * Twig es el único motor de plantillas configurado;

  * Doctrine ORM/DBAL está configurado;

  * SwiftMailer está configurado;

  * Las anotaciones están habilitadas para todo.

Esta viene preconfigurada con los siguientes paquetes:

  * **FrameworkBundle** — El paquete del núcleo de la plataforma *Symfony*

  * [**SensioFrameworkExtraBundle**][6] — Añade varias mejoras, incluyendo plantillas y la capacidad de enrutado en anotaciones

  * [**DoctrineBundle**][7] — Agrega compatibilidad para el *ORM* de *Doctrine*

  * [**TwigBundle**][8] — Agrega compatibilidad para el motor de plantillas *Twig*

  * [**SecurityBundle**][9] — Añade seguridad integrando los componentes de seguridad de *Symfony*

  * [**SwiftmailerBundle**][10] — Agrega soporte para `SwiftMailer`, una biblioteca para enviar mensajes de correo electrónico

  * [**MonologBundle**][11] — Agrega compatibilidad para *Monolog*, una biblioteca de registro cronológico

  * [**AsseticBundle**][12] — Agrega compatibilidad para *Assetic*, una biblioteca para procesar los activos

  * [**JMSSecurityExtraBundle**][13] — Permite añadir la seguridad vía anotaciones

  * [**JMSDiExtraBundle**][14] — Agrega muy potentes características de inyección de dependencias

  * **WebProfilerBundle** (en entornos dev/test) — Añade funcionalidad para la creación de perfiles y la barra de herramientas de depuración *web*

  * **SensioDistributionBundle** (en entornos dev/test) — Añade funcionalidad para configurar y trabajar con distribuciones *Symfony*

  * [**SensioGeneratorBundle**][15] (en entornos dev/test) — Añade la capacidad para generar código

  * **AcmeDemoBundle** (en entornos dev/test) — Un paquete de demostración con código de ejemplo

¡Diviértete!

[1]:  http://gitnacho.github.com/symfony-docs-es/book/installation.html
[2]:  http://getcomposer.org/
[3]:  http://symfony.com/download
[4]:  http://gitnacho.github.com/symfony-docs-es/quick_tour/the_big_picture.html
[5]:  http://gitnacho.github.com/symfony-docs-es/index.html
[6]:  http://gitnacho.github.com/symfony-docs-es/bundles/SensioFrameworkExtraBundle/index.html
[7]:  http://gitnacho.github.com/symfony-docs-es/book/doctrine.html
[8]:  http://gitnacho.github.com/symfony-docs-es/book/templating.html
[9]:  http://gitnacho.github.com/symfony-docs-es/book/security.html
[10]: http://gitnacho.github.com/symfony-docs-es/cookbook/email.html
[11]: http://gitnacho.github.com/symfony-docs-es/cookbook/logging/monolog.html
[12]: http://gitnacho.github.com/symfony-docs-es/cookbook/assetic/asset_management.html
[13]: http://jmsyst.com/bundles/JMSSecurityExtraBundle/master
[14]: http://jmsyst.com/bundles/JMSDiExtraBundle/master
[15]: http://gitnacho.github.com/symfony-docs-es/bundles/SensioGeneratorBundle/index.html
