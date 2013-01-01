ACTUALIZANDO DE 2.1 A 2.2
=========================

Pruebas funcionales
-------------------

 * El perfilador se ha deshabilitado de manera predeterminada en el entorno de pruebas. Lo puedes habilitar de nuevo modificando el archivo de configuración ``config_test.yml`` o incluso mejor, justo lo puedes habilitar para la próxima petición llamando a ``$client->enableProfiler()`` cuándo necesites el perfilador en una prueba (para acelerar un poco tus pruebas funcionales).
