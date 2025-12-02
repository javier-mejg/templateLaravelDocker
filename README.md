# Documentación acerca del proyecto y su estructura

Este repositorio utiliza Docker para orquestar todos los servicios usando imagenes (MariaDB + Adminer + Php Composer + Nginx + Laravel) y volumenes con datos persistentes, basándose en parte en el template de Laravel Sail.

## 1. Clonar el repositorio 

```bash
git clone https://github.com/javier-mejg/templateLaravelDocker.git
```

## 2. Integrar el archivo .env al proyecto 📃

(Este se envía por privado)

## 3. Ejecutar el archivo docker-compose-dev.yml 🐋

```bash
docker compose -f docker-compose-dev.yml up -d
```

## 4. Bajar contenedores

```bash
docker compose down
```

## 5. Instalar composer en el directorio de trabajo

```bash
composer install
```

## 6. Reemplazar al archivo de MsGraph.php

El que está en vendor/dcblogdev/laravel-microsoft-graph/src por el de overrides/vendor/dcblogdev/laravel-microsoft-graph/src

## 7. Ejecutar de nuevo el archivo docker-compose-dev.yml 🐋

```bash
docker compose -f docker-compose-dev.yml up -d
```

## 7. Poblar la base de datos

En Adminer, acceder a la BD, e importar el archivo "propedeutico.sql".