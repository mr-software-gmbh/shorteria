#!/bin/bash
docker container rm shorteria_mariadb -f
docker container rm shorteria -f

echo "y" | docker container prune
echo "y" | docker image prune -a
echo "y" | docker volume prune
