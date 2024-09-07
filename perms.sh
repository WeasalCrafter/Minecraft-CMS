#!/bin/bash

# Set permissions and ownership for /var/www/backups
sudo chmod -R 775 /var/www/backups/freetime
sudo chown www-data:www-data /var/www/backups/freetime

sudo chmod -R 775 /var/www/backups/competition
sudo chown www-data:www-data /var/www/backups/competition

# Set permissions and ownership for /var/www/paper
sudo chmod -R 775 /var/www/paper
sudo chown www-data:www-data /var/www/paper

# Set permissions and ownership for /var/www/html
sudo chmod -R 775 /var/www/html
sudo chown www-data:www-data /var/www/html

# Ser permissions and ownership for /var/www/paper/command.txt
sudo chown www-data:www-data /var/www/html/command.txt
sudo chmod 644 /var/www/html/command.txt

# Output success message
echo "Permissions Set Successfully"