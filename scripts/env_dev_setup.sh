#!/bin/bash
echo -e "# Set up dev environment"
php bin/console doctrine:database:drop --if-exists --force --env=dev
php bin/console doctrine:database:create --env=dev
php bin/console doctrine:schema:drop --force --env=dev
php bin/console doctrine:schema:create --env=dev
php bin/console doctrine:schema:validate --env=dev
echo -e " --> Done\n"