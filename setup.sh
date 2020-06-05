#! /bin/sh

echo '[1/2]Installing system dependenices...'
sudo apt-get update -y
sudo apt-get install php -y
sudo apt-get install composer -y
sudo apt-get install zip unzip php7.2-zip -y
sudo apt-get install php7.2-gmp -y

echo '[2/2]Installing project dependenices'
composer install

echo '🎉 Done!'