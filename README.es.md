
# ![alt](pix/mod_league.svg) mod_league

Copyright 2018 Miguel Romeral [Universidad de Alcalá](https://www.uah.es)

*También puedes leer esto en otros idiomas: [English](README.md), [Español](README.es.md)*

Módulo de actividad para Moodle que consiste en una liga en la que los alumnos pueden competir entre sí y llegar a lo más alto de la clasificación mediante las calificaciones a sus trabajos.

## Descripción

Este módulo consiste en una actividad que organiza los trabajos de los alumnos en función de la nota que se les asigne en forma de clasificación con el fin de fomentar la competencia entre estudiantes y el interés por el curso, los ejercicios a entregar y el contenido de la asignatura.

Una liga está compuesta por ejercicios que el administrador del curso (generalmente el profesor, administrador del sitio o un rol similar) va creando durante el curso.
Cada ejercicio está compuesto por un breve enunciado seguido de una profusa descripción, con el fin de indicar a los alumnos los criterios de corrección por cada ejercicio.
Los estudiantes pueden entonces mandar su tarea en ficheros para que el profesor pueda descargarlos y, en función de los criterios de corrección del ejercicio, calificarle de una determinada manera. Estas calificaciones se organizarán en una tabla con la clasificación general en cuanto a notas que los alumnos pueden ver en cualquier instante (manteniendo privacidad sobre las calificaciones de los demás alumnos).

La privacidad en la clasificación consiste en que los profesores tienen acceso a cualquier dato de los trabajos de los estudiantes mientras que éstos únicamente podrán ver los datos de sus propios trabajos, además sólo podrán ver la nota global de los demás usuarios y sus nombres están ocultos mediante una función hash, de esta manera se consigue privacidad entre los diferentes estudiantes.

## Motivación

Este proyecto consiste en el Trabajo ( *ACTUALMENTE EN DESARROLLO* ) de Fin de Grado de Miguel Ángel García Romeral con la supervisión del [Profesor Antonio García Cabot](https://www.uah.es/es/estudios/profesor/Antonio-Garcia-Cabot/), de la [Universidad de Alcalá](https://www.uah.es) .

## Requisitos

* Versión de Moodle 3.1.12 o superior.
* Versión de PHP 5.3 o superior.

## Instalación

1. Descargue el repositorio desde el botón *Clone or download* -> *Download ZIP* o clone el proyecto mediante la siguiente orden:

    ```
    git clone https://github.com/miguelromeral/moodle-mod_league.git
    ```

2. Cree un subdirectorio llamado ```league``` dentro del directorio ```(DIRECTORIO_MOODLE)/mod/``` y copie los ficheros descargados dentro. Normalmente ```(DIRECTORIO_MOODLE)``` se suele encontrar en ```/var/www/html/moodle```, aunque depende de cada servidor.

3. Inicie sesión como *Administrador* en su sitio Moodle y acceda a *Administración del sitio* -> *Notificaciones*.

4. Si todo fue correcto, aparecerá en pantalla un solicitando atención (será nuestra *Liga* lista para instalarse). Pulsar sobre *Actualizar base de datos Moodle ahora*.

5. Una vez finalizado, puede pulsar en *Continuar* y a partir de entonces puede crear instancias del plugin en cualquier curso (a través de *Activar edición*).

## Funcionalidades y capturas

*Actualmente, las funcionalidades y capturas están en desarrollo. Disculpe las molestias.*

[//]: # (## Recomendaciones)

## Pruebas

*Actualmente la aplicación está bajo pruebas.*

## Manual de usuario

*Actualmente, el manual de usuario está siendo escrito. Disculpe las molestias.*

## TODO

* Sincronizar ejercicios con Calendar API y poner fecha límite a los mismos (desactivación automática).
* Configurar plugin para que solo admita determinados tipos de ficheros.

## Contacto

Puede enviar sus comentarios, sugerencias o informar de un error a través de los siguientes enlaces de contacto.

Correo electrónico: miguelangel.garciar@edu.uah.es

LinkedIn: Miguel Romeral (https://www.linkedin.com/in/miguelromeral/)

Twitter: @MiguelRomeral (https://twitter.com/MiguelRomeral)

## Licencia

Licencia del plugin: Licencia Pública General GNU 3.0

Licencia de Moodle: Moodle is provided freely as open source software, under the GNU General Public License <https://docs.moodle.org/dev/License>.