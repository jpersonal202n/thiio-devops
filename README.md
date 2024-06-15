# Proyecto con Docker y Pruebas Automatizadas

Este documento proporciona los pasos necesarios para configurar e inicializar un entorno de desarrollo utilizando Docker y Laravel.

## Índice

1. [Requisitos Previos](#requisitos-previos)
2. [Configuración Inicial](#configuración-inicial)
3. [Comandos Docker](#comandos-docker)
6. [Pruebas Automatizadas](#pruebas-automatizadas)

## Requisitos Previos

Asegúrate de tener instalados los siguientes programas en tu sistema:

- [Docker](https://www.docker.com/products/docker-desktop)
- [Docker Compose](https://docs.docker.com/compose/install/)
- [Composer](https://getcomposer.org/)

## Configuración Inicial

1. **Clonar el repositorio**:
   ```bash
   git clone https://github.com/jpersonal202n/thiio-devops.git
   cd thiio-devops

2. **Copiar el archivo de configuración de entorno:**:
   ```bash
   cp .env.example .env

3. **Ejecute el comando**
   ```bash
   sudo chmod -R 777 ./storage

## Comandos Docker
1. **Construir y levantar los servicios:**
   ```bash
   docker-compose up --build

2. **Para ingresar al contendor de th_laravel añade el siguiente comand (opcional):**
   ```bash
   docker exec -it th_laravel bash 

3. **Ejecute los siguientes comandos:**
   ```bash
   php artisan passport:install --force

## Pruebas automatizadas

1. **Ingresa al contenedor de th_laravel:**
   ```bash
   docker exec -it th_laravel bash 

1. **Ejecuta el siguiente comando**
   ```bash
   php artisan test
