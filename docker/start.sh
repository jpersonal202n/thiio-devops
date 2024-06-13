#!/bin/bash

RED='\033[0;31m'
GREEN='\033[0;32m'
YELLOW='\033[1;33m'
NC='\033[0m'

echo -e "${YELLOW}Esperando a que la base de datos esté disponible...${NC}"
RETRIES=5
until nc -z th_db 3306; do
  echo -e "${RED}No se puede conectar a la base de datos. Reintentando en 5 segundos...${NC}"
  sleep 5
  ((RETRIES--))
  if [ $RETRIES -le 0 ]; then
    echo -e "${RED}No se pudo conectar a la base de datos después de varios intentos. Abortando.${NC}"
    exit 1
  fi
done

if php artisan migrate:status >/dev/null 2>&1; then
  echo -e "${GREEN}Las migraciones ya están aplicadas. No se requieren acciones adicionales.${NC}"
else
  echo -e "${YELLOW}Ejecutando migraciones...${NC}"
  if php artisan migrate --force; then
    echo -e "${GREEN}Migraciones ejecutadas exitosamente.${NC}"
  else
    echo -e "${RED}Error al ejecutar migraciones.${NC}"
    exit 1
  fi

  echo -e "${YELLOW}Ejecutando seeders...${NC}"
  if php artisan db:seed --force; then
    echo -e "${GREEN}Seeders ejecutados exitosamente.${NC}"
  else
    echo -e "${RED}Error al ejecutar seeders.${NC}"
    exit 1
  fi
fi

echo -e "${GREEN}Iniciando PHP-FPM...${NC}"
php-fpm
