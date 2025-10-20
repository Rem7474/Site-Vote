#!/bin/bash
git clone https://github.com/Rem7474/Site-Vote.git /var/www/html/vote
mv /var/www/html/vote/* /var/www/html
rm /var/www/html/private/parametres.ini
cp /var/www/html/parametres2.ini /var/www/html/private/parametres.ini