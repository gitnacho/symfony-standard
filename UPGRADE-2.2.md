ACTUALIZANDO DE 2.1 A 2.2
=========================

 * La ruta ``_internal`` no se utiliza más. Esta será removida de ambas configuraciones, enrutado y seguridad. Se añadió una clave ``router_proxy`` a la configuración de ``framework`` y se tiene que especificar al usar ESI o Hinclude. Ninguna configuración de seguridad es requerida para esta ruta puesto que de manera predeterminada el acceso ESI es permitido únicamente para servidores confiables y el acceso Hinclude utiliza una mecanismo de firma URL.

   ```
   framework:
       # ...
       router_proxy: { path: /_proxy }
   ```

Pruebas funcionales
-------------------

 * El perfilador se ha deshabilitado de manera predeterminada en el entorno de pruebas. Lo puedes habilitar de nuevo modificando el archivo de configuración ``config_test.yml`` o incluso mejor, justo lo puedes habilitar para la próxima petición llamando a ``$client->enableProfiler()`` cuándo necesites el perfilador en una prueba (para acelerar un poco tus pruebas funcionales).
