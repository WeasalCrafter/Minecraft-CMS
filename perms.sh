#!/bin/bash

# Set permissions and ownership for /var/www/backups
sudo chmod -R 775 /var/www/backups
sudo chown www-data:www-data /var/www/backups

# Set permissions and ownership for /var/www/paper
sudo chmod -R 775 /var/www/paper
sudo chown www-data:www-data /var/www/paper

# Set permissions and ownership for /var/www/html
sudo chmod -R 775 /var/www/html
sudo chown www-data:www-data /var/www/html

# Output success message
echo "Permissions Set Successfully"
