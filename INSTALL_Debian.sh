#!/bin/bash

echo -e "\nWELCOME TO THE NOTES API INSTALLATION WIZARD"

touch mysql/db_config.php

echo -e "\nDATABASE PREPARATION"

echo -e "\nEnter your MySQL credentials"
echo "These credentials are only for the installation process — they won't be used by the app."
echo -n "MySQL user         : "; read MYSQL_USER
read -sp "MySQL password     : " MYSQL_PASS; echo

echo -e "\nEnter your MySQL configuration for the application"

echo -e "\nDatabase name"
echo -n "Enter database name = "; read DB_NAME

sudo mysql -u $MYSQL_USER --password=$MYSQL_PASS -e "CREATE DATABASE IF NOT EXISTS \`$DB_NAME\`"

sudo mysql -u $MYSQL_USER --password=$MYSQL_PASS $DB_NAME -e "CREATE TABLE IF NOT EXISTS \`notes\` (
    \`id\` CHAR(16) PRIMARY KEY,
    \`title\` VARCHAR(255) DEFAULT NULL,
    \`body\` TEXT DEFAULT NULL,
    \`tags\` VARCHAR(255) DEFAULT NULL,
    \`createdAt\` CHAR(24) NOT NULL,
    \`updatedAt\` CHAR(24) DEFAULT NULL
)"

echo -e "\nDatabase user credentials"
echo -n "Enter a MySQL username for this app = "; read DB_USER
echo -n "Enter a MySQL password for this app = "; read -s DB_PASS; echo
sudo mysql -u $MYSQL_USER --password=$MYSQL_PASS -e "CREATE USER IF NOT EXISTS '${DB_USER}'@'localhost' IDENTIFIED BY '${DB_PASS}'"
sudo mysql -u $MYSQL_USER --password=$MYSQL_PASS -e "GRANT SELECT, INSERT, UPDATE, DELETE ON \`$DB_NAME\`.\`notes\` TO \`$DB_USER\`@\`localhost\`"


echo -e "\nPreparing database configuration ..."
echo "<?php" > mysql/db_config.php
echo "    define(\"DB_USER\", \"$DB_USER\");" >> mysql/db_config.php
echo "    define(\"DB_PASS\", \"$DB_PASS\");" >> mysql/db_config.php
echo "    define(\"DB_NAME\", \"$DB_NAME\");" >> mysql/db_config.php
echo "?>" >> mysql/db_config.php

echo " Done"

echo -e "\nVIRTUAL HOST CONFIGURATION FILE"
echo -n "Enter the VirtualHost Configuration file name (Default: attendance-app.conf): "
read VH_NAME
if [[ -z "$VH_NAME" ]]; then
    VH_NAME="notes-app-api.conf"
else
    VH_NAME="$VH_NAME"
fi
echo -e "\nCreating VirtualHost configuration file ..."
sudo tee /etc/apache2/sites-available/$VH_NAME > /dev/null <<EOF
<VirtualHost *:80>
    #ServerName notes-app-api.local

    ServerAdmin webmaster@localhost
    DocumentRoot `pwd`

    Header set Access-Control-Allow-Origin "*"
	Header set Access-Control-Allow-Headers "*"
	Header set Access-Control-Allow-Methods "PUT, GET, POST, DELETE, OPTIONS"
	Header set Access-Control-Max-Age "300"

    <Directory `pwd`>
    	RewriteEngine On
    	RewriteRule ^([A-Za-z0-9\/_-]+)$ index.php
    </Directory>

    ErrorLog \${APACHE_LOG_DIR}/error.log
    CustomLog \${APACHE_LOG_DIR}/access.log combined
</VirtualHost>
EOF
echo " Done"

echo -e "\nEnabling VirtualHost configuration file ..."
sudo a2ensite $VH_NAME
echo " Done"

echo -e "\nEnabling Apache mod_rewrite module ..."
sudo a2enmod rewrite
echo " Done"

echo -e "\nEnabling Apache mod_headers module ..."
sudo a2enmod headers
echo " Done"

echo -e "\nReloading Apache2 HTTP Server ..."
sudo systemctl reload apache2
echo " Done"

unset MYSQL_USER
unset MYSQL_PASS
unset DB_NAME
unset DB_USER
unset DB_PASS
unset VH_NAME

echo -e "\nINSTALLATION FINISHED"