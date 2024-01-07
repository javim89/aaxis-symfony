# README

Este README proporciona las instrucciones necesarias para configurar y ejecutar el proyecto desarrollado con Symfony.

## Requisitos Previos

Asegúrate de tener instalados los siguientes requisitos antes de comenzar:

- [PHP](https://www.php.net/manual/en/install.php) (se recomienda la versión 7.4 o superior)
- [Composer](https://getcomposer.org/download/)
- [Symfony](https://symfony.com/download)
- [MySQL](https://dev.mysql.com/downloads/)

## Configuración del Proyecto

1. Clona el repositorio desde GitHub:

   ```bash
   git clone https://github.com/javim89/aaxis-symfony.git
   ```
2. Accede al repositorio
    ```bash
   cd  aaxis-symfony
   ```
3. Instala las dependencias del proyecto utilizando Composer:
    ```bash
    composer install
    ```
4. Crea el archivo de configuración de entorno (.env) basado en el ejemplo proporcionado y modifica la configuracion de la base de datos:
    ```bash
    cp .env.example .env
    ```
5. Genera las claves SSH necesarias para la generación de tokens:
    ```bash
    php bin/console lexik:jwt:generate-keypair
    ```
## Configuración de la base de datos

1. Crea la base de datos especificada en tu archivo .env:
    ```bash
    php bin/console doctrine:database:create
    ```
2. Ejecuta las migraciones para crear las tablas de la base de datos:
    ```bash
    php bin/console doctrine:migrations:migrate
    ```

## Ejecucion servidor local

1. Para iniciar el servidor de desarrollo de Symfony, ejecuta el siguiente comando:
    ```bash
    symfony server:start
    ```
2. Dar de alta un usuario (pasar email y password en el payload):
   ```bash
    localhost:8000/register
    ```
3. Login (pasar email y password en el payload)
   ```bash
    localhost:8000/login
    ```
4. Swagger de la app
   ```bash
    localhost:8000/api/doc
    ```
