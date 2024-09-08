#!/bin/bash

set -e

# Update and install required packages
sudo apt update && sudo apt upgrade -y
sudo apt install git curl unzip zip openjdk-21-jdk apache2 php php-zip -y

# Firewall configuration
read -p "Do you want to configure UFW firewall rules? (y/n): " configure_ufw
if [[ $configure_ufw == "y" ]]; then
    sudo ufw allow 'Apache'
    sudo ufw allow 'OpenSSH'
    sudo ufw allow 22
    sudo ufw allow 25565
    sudo ufw enable
    sudo ufw status
fi

# Prompt for RAM in GB and convert to MB
read -p "Enter the amount of RAM (in GB) you want to allocate for PaperMC (e.g., 4 for 4GB): " ram_gb
ram_mb=$(($ram_gb * 1024))

# Setup PaperMC server directory and download server.jar
sudo mkdir -p /var/www/paper
sudo curl -o /var/www/paper/server.jar https://api.papermc.io/v2/projects/paper/versions/1.21.1/builds/69/downloads/paper-1.21.1-69.jar

# Generate and configure server start script with converted RAM
cat <<EOT | sudo tee /var/www/paper/start.sh
#!/bin/bash

java -Xms${ram_mb}M -Xmx${ram_mb}M --add-modules=jdk.incubator.vector -XX:+UseG1GC -XX:+ParallelRefProcEnabled -XX:MaxGCPauseMillis=200 -XX:+UnlockExperimentalVMOptions -XX:+DisableExplicitGC -XX:+AlwaysPreTouch -XX:G1HeapWastePercent=5 -XX:G1MixedGCCountTarget=4 -XX:InitiatingHeapOccupancyPercent=15 -XX:G1MixedGCLiveThresholdPercent=90 -XX:G1RSetUpdatingPauseTimePercent=5 -XX:SurvivorRatio=32 -XX:+PerfDisableSharedMem -XX:MaxTenuringThreshold=1 -Dusing.aikars.flags=https://mcflags.emc.gs -Daikars.new.flags=true -XX:G1NewSizePercent=30 -XX:G1MaxNewSizePercent=40 -XX:G1HeapRegionSize=8M -XX:G1ReservePercent=20 -jar /var/www/paper/server.jar --nogui
EOT

sudo chmod +x /var/www/paper/start.sh
sudo chown -R www-data:www-data /var/www/paper
sudo chmod -R 755 /var/www/paper

# Run PaperMC server for the first time to generate files
cd /var/www/paper
sudo -u www-data /bin/bash /var/www/paper/start.sh || true
sudo sed -i 's/eula=false/eula=true/' /var/www/paper/eula.txt

# Edit server.properties for enable-rcon, rcon-password, and level-name
sudo sed -i 's/enable-rcon=false/enable-rcon=true/' /var/www/paper/server.properties
sudo sed -i 's/rcon.password=.*/rcon.password=strong-password/' /var/www/paper/server.properties
sudo sed -i 's/level-name=.*/level-name=freetime/' /var/www/paper/server.properties

# Setup systemd service for PaperMC
sudo bash -c 'cat <<EOL > /etc/systemd/system/paper.service
[Unit]
Description=Minecraft Server
After=network.target

[Service]
User=www-data
Group=www-data
WorkingDirectory=/var/www/paper
ExecStart=/bin/bash /var/www/paper/start.sh
TimeoutStartSec=600

[Install]
WantedBy=multi-user.target
EOL'

sudo systemctl daemon-reload
# sudo systemctl start paper

if [ -f /var/www/html/index.html ]; then
    sudo rm /var/www/html/index.html
    echo "Removed /var/www/html/index.html"
fi

# Allow www-data user to control the service without password
sudo bash -c 'echo "www-data ALL=NOPASSWD: /bin/systemctl stop paper, /bin/systemctl start paper" >> /etc/sudoers'

# Clean up default Apache index page and clone the CMS
cd /var/www/html
# sudo rm -rf /var/www/html/*
# sudo rm -rf /var/www/html/.[!.]* /var/www/html/*
# sudo git clone https://github.com/WeasalCrafter/Minecraft-CMS.git .

# Set permissions for the CMS
sudo chmod +x perms.sh
sudo mkdir -p /var/www/backups/freetime /var/www/backups/competition
sudo ./perms.sh

# Prompt for panel password and update login.php
# read -p "Enter a password for the Minecraft CMS panel: " panel_password
# sudo sed -i "s/\$_POST\['password'\] = .*/\$_POST['password'] = '$panel_password';/" /var/www/html/login.php

# Instructions to access the server
IP=$(hostname -I | awk '{print $1}')
echo "Installation complete! Access the Minecraft CMS at http://$IP"
echo "The password is 'admin'"
