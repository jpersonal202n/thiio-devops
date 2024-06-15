# Proyecto con Docker y Pruebas Automatizadas

Este documento detalla el proceso de configuración de un entorno de desarrollo con Docker, la implementación de un CRUD de usuarios utilizando Laravel, y la configuración de pruebas automatizadas con PHPUnit.

## Índice

1. [Introducción](#introducción)
2. [Configuración del Entorno con Docker](#configuración-del-entorno-con-docker)
    - [Servicios Utilizados](#servicios-utilizados)
    - [Archivo docker-compose.yml](#archivo-docker-composeyml)
3. [Inicialización del Proyecto](#inicialización-del-proyecto)
    - [Configuraciones de Laravel](#configuraciones-de-laravel)
    - [Comandos Docker](#comandos-docker)
4. [Pruebas Automatizadas](#pruebas-automatizadas)
5. [Conclusiones](#conclusiones)

## Introducción

Este documento explica cómo se configuró un entorno de desarrollo con Docker, se implementó un CRUD de usuarios y se realizaron pruebas automatizadas para asegurar que todo funcione correctamente.

## Configuración del Entorno con Docker

### Servicios Utilizados

Se usaron estos servicios para el proyecto:

- **Nginx**: Servidor web y proxy inverso.
- **MySQL**: Base de datos relacional.
- **PHP**: Entorno de ejecución para el framework Laravel.
- **HTTPD**: Servicio HTTP adicional.

### Archivo docker-compose.yml

Se utilizó el archivo `docker-compose.yml` para definir y gestionar los servicios Docker. Aquí está el contenido del archivo:

```yaml
version: '3.8'

services:
  th_laravel:
    build:
      context: ./
      dockerfile: Dockerfile
    image: th_laravel_latest
    container_name: th_laravel
    working_dir: /var/www
    volumes:
      - .:/var/www
    environment:
      - PHP_OPCACHE_VALIDATE_TIMESTAMPS=0
    restart: unless-stopped
    privileged: true
    tty: true
    networks:
      - th_network

  th_nginx_proxy:
    image: nginx:stable-alpine
    container_name: th_nginx_latest
    restart: unless-stopped
    tty: true
    depends_on:
      - th_laravel
    ports:
      - "80:80"
    volumes:
      - .:/var/www
      - ./nginx/default.conf:/etc/nginx/conf.d/default.conf
    networks:
      - th_network

  th_db:
    image: mysql:8.0.21
    container_name: th_mysql_latest
    restart: always
    tty: true
    environment:
      MYSQL_DATABASE: thiio
      MYSQL_ROOT_PASSWORD: secret
    ports:
      - "33061:3306"
    volumes:
      - dbdata:/var/lib/mysql:delegated
    command:
      - --default-authentication-plugin=mysql_native_password
      - --sort_buffer_size=1073741824
      - --max_connections=1000
    networks:
      - th_network

  th_random_http_service:
    image: httpd:latest
    container_name: th_random_http_service
    restart: unless-stopped
    tty: true
    depends_on:
      - th_laravel
    ports:
      - "8080:80"
    networks:
      - th_network

volumes:
  dbdata:
    driver: local

networks:
  th_network:
    driver: bridge
