
# Instrucciones

La aplicación se ha cargado dentro de un contenedor Docker para un despliegue más sencillo. En caso de querer ejecutarlo sin Docker, habrá que cambiar el archivo `.env` dentro del proyecto para configurar la conexión a la base de datos.

## Cargar la Aplicación con Docker

Para iniciar la aplicación con Docker, sigue estos pasos:

1. Levanta los servicios con `docker-compose`:
   ```sh
   docker-compose up -d
   ```

2. Una vez que los servicios estén en funcionamiento, podrás acceder a la aplicación en:
   ```
   http://localhost:9000
   ```

## Ejecutar PhpStorm dentro del Contenedor

Para ejecutar PhpStorm directamente dentro del contenedor en un Mac, sigue estos pasos:

1. Instala XQuartz en tu Mac.
2. Una vez instalado XQuartz, ejecuta PhpStorm dentro del contenedor con el siguiente comando:
   ```sh
   bash /opt/PhpStorm-232.10300.41/bin/phpstorm.sh
   ```

Esto te permitirá usar PhpStorm desde dentro del contenedor, facilitando el desarrollo y la depuración de la aplicación.

---

En caso de que quieras ejecutar la aplicación sin Docker, no olvides modificar el archivo `.env` para configurar la conexión correcta a la base de datos.
