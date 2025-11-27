FROM php:8.2-fpm

# 1. Instalar paquetes del sistema y extensiones PHP
RUN apt-get update && apt-get install -y \
    git unzip libpng-dev libzip-dev libonig-dev libxml2-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && rm -rf /var/lib/apt/lists/*

# 2. Directorio de trabajo (el mismo que usas en docker-compose)
WORKDIR /var/www

# 3. Copiar el código de la aplicación (para prod / Azure)
#    En dev esto luego se sobrescribe con el volumen ./:/var/www
COPY . .

# 4. Sobrescribir el archivo de la librería (una vez que vendor exista en el contexto)
#    Asumiendo que vendor está en /var/www/vendor
COPY overrides/vendor/dcblogdev/laravel-microsoft-graph/src/MsGraph.php \
     vendor/dcblogdev/laravel-microsoft-graph/src/MsGraph.php
