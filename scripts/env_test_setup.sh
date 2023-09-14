#!/bin/bash
echo -e "# Set up test environment and play test"
php bin/console doctrine:database:drop --if-exists --force --env=test
php bin/console doctrine:database:create --env=test
php bin/console doctrine:schema:drop --force --env=test
php bin/console doctrine:schema:create --env=test
php bin/console doctrine:schema:validate --env=test
php bin/phpunit
echo -e " --> Done\n"