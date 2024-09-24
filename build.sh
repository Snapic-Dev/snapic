#!/bin/bash
# Script para iniciar os containers do Docker

echo "Iniciando o container MySQL..."
docker start mysql_container

echo "Iniciando o container Backend (PHP + Apache)..."
docker start snapic_container

echo "Iniciando o container phpMyAdmin..."
docker start phpmyadmin_container

echo "Todos os containers foram iniciados."
