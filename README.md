Edición estándar de Symfony
===========================

Bienvenidos a la edición estándar de Symfony - una aplicación Symfony2
totalmente funcional que puedes utilizar como el esqueleto de tu nueva
aplicación. Si deseas obtener más información sobre las características
incluidas, consulta la sección "¿Qué hay dentro?".

Este documento contiene información sobre cómo descargar y comenzar a
utilizar Symfony.  Para una explicación más detallada, ve el capítulo
[Instalando](http://gitnacho.github.com/symfony-docs-es/book/installation.html)
de la documentación de Symfony.

1) Descarga la edición estándar
-------------------------------

Si ya has descargado la edición estándar, y la desempacaste en alguna parte
dentro de tu directorio web raíz, entonces ve a la sección de "Instalación".

Para descargar la edición estándar, tienes dos opciones:

### Descargar un archivo (*recomendado*)

La forma más sencilla de empezar es descargando un archivo de la edición
estándar desde (http://symfony.com/download). Descomprímelo en algún lugar
bajo el directorio web raíz de tu servidor y listo. La raíz del directorio
web es donde tu servidor web (por ejemplo Apache) accede a
`http://localhost` en un navegador.

### Clonar el repositorio Git

Te recomendamos que descargues la versión empaquetada de esta distribución.
Pero si todavía quieres usar Git, lo harás por cuenta propia.

Ejecuta las siguientes instrucciones:

    git clone http://github.com/gitnacho/symfony-standard.git
    cd symfony-standard
    rm -rf .git

2) Instalando
-------------

Una vez hayas descargado la edición estándar, la instalación es fácil, y
básicamente consiste en asegurarte de que tu sistema está listo para
Symfony.

### a) Instala las bibliotecas de proveedores

Si descargaste el archivo "sin vendors" o lo instalaste a través de git,
entonces necesitas descargar todas las bibliotecas de proveedores. Si no
estás seguro de si necesitas hacer esto, comprueba si tienes un directorio
``vendor/``.  Si no es así, o si ese directorio está vacío, descarga el
composer siguiendo las instrucciones de http://getcomposer.org/ y ejecuta la
siguiente orden:

    php composer.phar install

### b) Comprueba la configuración de tu sistema

Ahora, asegúrate de que tu sistema local está correctamente configurado para
Symfony. Para ello, ejecuta la siguiente instrucción:

    php app/check.php

Si recibes advertencias o recomendaciones, entonces aplica las correcciones
adecuadas antes de continuar.

### c) Accede a la aplicación a través del navegador

¡Enhorabuena! Ahora estás listo para usar Symfony. Si descomprimiste Symfony
en la raíz de tu directorio web, entonces deberías poder acceder a la
versión web para comprobar los requisitos de Symfony a través de:

    http://localhost/Symfony/web/config.php

Si todo se ve bien, haz clic en la opción "Pospón la configuración y llévame
a la página de bienvenida" para cargar tu primer página Symfony.

También puedes utilizar un configurador basado en web haciendo clic en el
enlace a "Configura en línea tu aplicación Symfony" de la página
``config.php``.

Para ver en acción páginas Symfony reales, accede a la siguiente página:

    web/app_dev.php/demo/hello/Symfony

3) ¡Aprende Symfony!
--------------------

Esta distribución, no sólo está destinada a ser el punto de partida para tu
aplicación, sino que además contiene código de ejemplo con el cual puedes
juguetear para aprender.

Una gran manera para comenzar a aprender Symfony además de la
[Guía de inicio rápido](http://gitnacho.github.com/symfony-docs-es/quick_tour/the_big_picture.html),
que te llevará a través de todas las características básicas de Symfony2 y
las páginas de prueba que están disponibles en la edición estándar.

Una vez que te sientas cómodo, puedes seguir con la lectura del
[libro oficial de Symfony2](http://gitnacho.github.com/symfony-docs-es/).

Usando esta edición como la base de tu aplicación
-------------------------------------------------

Debido a que la edición estándar está totalmente configurada y viene con
algunos ejemplos, tendrás que hacer algunos cambios antes de usarla para
construir tu aplicación.

La distribución viene preconfigurada con los siguientes valores:

* Twig es el único motor de plantillas configurado;
* Doctrine ORM/DBAL está configurado;
* SwiftMailer está configurado;
* Las anotaciones están habilitadas para todo.

Un paquete predefinido, ``AcmeDemoBundle``, muestra a Symfony2 en acción. 
Después de jugar con él, lo puedes eliminar siguiendo estos pasos:

* elimina el directorio ``src/Acme``;
* quita las entradas de enrutado que hacen referencia a ``AcmeBundle`` en
   ``app/config/routing_dev.yml``;
* quita el ``AcmeBundle`` de los paquetes registrados en
  ``app/AppKernel.php``;
* borra el directorio ``web/bundles/acmedemo``.

¿Qué hay dentro?
----------------

La edición estándar de Symfony viene preconfigurada con los siguientes
paquetes:

* **FrameworkBundle** - El paquete del núcleo de la plataforma Symfony
* **SensioFrameworkExtraBundle** - Añade varias mejoras, incluyendo
  plantillas y la capacidad enrutado desde anotaciones
  ([documentación](http://gitnacho.github.com/symfony-docs-es/bundles/SensioFrameworkExtraBundle/index.html))
* **DoctrineBundle** - Agrega compatibilidad para el ORM de Doctrine
  ([documentación](http://gitnacho.github.com/symfony-docs-es/book/doctrine.html))
* **TwigBundle** - Agrega compatibilidad para el motor de plantillas Twig
  ([documentación](http://gitnacho.github.com/symfony-docs-es/book/templating.html))
* **SecurityBundle** - Añade seguridad integrando los componentes de
  seguridad de Symfony
  ([documentación](http://gitnacho.github.com/symfony-docs-es/book/security.html))
* **SwiftmailerBundle** - Agrega soporte para SwiftMailer, una biblioteca
  para enviar mensajes de correo electrónico
  ([documentación](http://gitnacho.github.com/symfony-docs-es/cookbook/email.html))
* **MonologBundle** - Agrega compatibilidad para Monolog, una biblioteca de
  registro
  ([documentación](http://gitnacho.github.com/symfony-docs-es/cookbook/logging/monolog.html))
* **AsseticBundle** - Agrega soporte para Assetic, una biblioteca para el
  procesamiento de los activos
  ([documentación](http://gitnacho.github.com/symfony-docs-es/cookbook/assetic/asset_management.html))
* **JMSSecurityExtraBundle** - Te permite definir la seguridad de tu
  aplicación a través de anotaciones
  ([documentación](http://gitnacho.github.com/symfony-docs-es/bundles/JMSSecurityExtraBundle/index.html))
* **WebProfilerBundle** (en entornos dev/test) - Añade funcionalidad para la
  creación de perfiles y la barra de herramientas de depuración web
* **SensioDistributionBundle** (en entornos dev/test) - Añade funcionalidad
  para configurar y trabajar con distribuciones Symfony
* **SensioGeneratorBundle** (en entornos dev/test) - Añade la capacidad para
  generar código
  ([documentación](http://gitnacho.github.com/symfony-docs-es/bundles/SensioGeneratorBundle/index.html))
* **AcmeDemoBundle** (en entornos dev/test) - Un paquete de demostración con
    código de ejemplo

¡Diviértete!
