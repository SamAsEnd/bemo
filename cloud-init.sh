#!/bin/bash

# Configuration
SAM="sam"
PASSWORD="<-- CHANGE-ME -->"
TOKEN="<-- CHANGE-ME -->"

DB_NAME="<-- CHANGE-ME -->"
DB_USERNAME="<-- CHANGE-ME -->"
DB_PASSWORD="<-- CHANGE-ME -->"

BRANCH="master"

HOSTNAME=$(curl -s http://169.254.169.254/metadata/v1/hostname)

# Basic configurations
apt-get update
timedatectl set-timezone "Africa/Addis_Ababa"

# New user setup
useradd -m -p `openssl passwd -crypt $PASSWORD` -s /bin/bash $SAM
usermod -aG sudo $SAM

# Copy SSH access
mkdir -p /home/$SAM/.ssh
cp ~/.ssh/authorized_keys /home/$SAM/.ssh/
chown -R $SAM:$SAM /home/$SAM/

# Setup firewall
ufw app list
ufw allow OpenSSH
yes | ufw enable
ufw status

## Nginx
add-apt-repository --yes ppa:ondrej/nginx
apt update
apt install nginx-full --yes

ufw app list
ufw allow 'Nginx Full'
ufw status

cat > /etc/nginx/sites-available/default << EOF
server {
    root /home/$SAM/bemo/public;
    index index.php;
    server_name $HOSTNAME;

    add_header X-Frame-Options "SAMEORIGIN";
    add_header Referrer-Policy "strict-origin-when-cross-origin";
    add_header X-Content-Type-Options "nosniff";
    add_header X-XSS-Protection "1; mode=block";
    add_header Strict-Transport-Security "max-age=63072000; includeSubDomains" always;

    charset utf-8;

    location / {
        try_files \$uri \$uri/ /index.php?\$query_string;
    }

    location ~* ^/(?:js|css|fonts|images/vendor) {
        expires 1y;
        access_log off;
        add_header Cache-Control "public";
    }

    location ~* \.png\$ {
        expires 1y;
        access_log off;
        add_header Cache-Control "public";
    }

    location = /favicon.ico { access_log off; log_not_found off; }
    location = /robots.txt  { access_log off; log_not_found off; }

    error_page 404 /index.php;

    location ~ \.php$ {
        fastcgi_pass unix:/var/run/php/php8.0-fpm.sock;
        fastcgi_param SCRIPT_FILENAME \$realpath_root\$fastcgi_script_name;
        include fastcgi_params;
    }

    location ~ /\.(?!well-known).* {
            deny all;
    }

    location ~ /\.ht {
            deny all;
    }
}
EOF

# MariaDB
apt install mariadb-server expect --yes
expect -c "
set timeout 3
spawn mysql_secure_installation
expect \"Enter current password for root (enter for none):\"
send \"\r\"
expect \"root password?\"
send \"n\r\"
expect \"Remove anonymous users?\"
send \"y\r\"
expect \"Disallow root login remotely?\"
send \"y\r\"
expect \"Remove test database and access to it?\"
send \"y\r\"
expect \"Reload privilege tables now?\"
send \"y\r\"
expect eof
"

mysql -u root -e "CREATE DATABASE $DB_NAME;"
mysql -u root -e "GRANT ALL ON $DB_NAME.* TO '$DB_USERNAME'@'localhost' IDENTIFIED BY '$DB_PASSWORD' WITH GRANT OPTION;"
mysql -u root -e "FLUSH PRIVILEGES;"

# PHP
add-apt-repository --yes ppa:ondrej/php
apt update

apt install --yes brotli curl git redis unzip \
    php8.0-bcmath \
    php8.0-cli \
    php8.0-curl \
    php8.0-fpm \
    php8.0-mbstring \
    php8.0-mysql \
    php8.0-redis \
    php8.0-xml \
    php8.0-zip

# NPM
snap install node --classic

# Supervisor
apt install --yes supervisor
cat > /etc/supervisor/conf.d/laravel-worker.conf << EOF
[program:laravel-worker]
process_name=%(program_name)s_%(process_num)02d
command=php /home/$SAM/bemo/artisan queue:work --sleep=3 --tries=3
autostart=true
autorestart=true
user=$SAM
numprocs=8
redirect_stderr=true
stdout_logfile=/home/$SAM/app/worker/worker.log
stopwaitsecs=3600
EOF

# Brotli
wget https://gist.githubusercontent.com/majal/82aa3de30c163fc624a05eb02d72f12c/raw/20221ae511a338fb8176728d14ad0169f3bb6fac/mkbrotli
chmod +x mkbrotli
./mkbrotli
apt install --yes brotli

# Composer
wget https://getcomposer.org/composer.phar
chmod +x composer.phar
mv composer.phar /usr/local/bin/composer

# Project
cd /home/$SAM
mkdir -p bemo/public app/logs app/worker releases

cat > /home/$SAM/app/.env << EOF
APP_KEY="<-- php artisan key:generate -->"
APP_URL=https://$HOSTNAME

DB_DATABASE=$DB_NAME
DB_USERNAME=$DB_USERNAME
DB_PASSWORD=$DB_PASSWORD

SESSION_COOKIE="__Host-bemo"

EOF

echo "deploying" > bemo/public/index.php

chown -R $SAM:$SAM bemo app releases bemo.git
chmod -R 777 app
systemctl restart nginx

sudo -i -u $SAM bash << EOF
    cd /home/$SAM
    git clone --bare https://github.com/SamAsEnd/bemo.git /home/$SAM/bemo.git

    cd bemo.git
    git worktree add /home/$SAM/deleteme master
    mv /home/$SAM/deleteme /home/$SAM/delete
    git worktree prune

    cd /home/$SAM/delete
    composer install --no-dev
    ln -nfs /home/$SAM/app/.env .env
    php artisan key:generate --force
    sed 's/$SAM@$HOSTNAME/127.0.0.1/g' Envoy.blade.php > /home/$SAM/Local.blade.php
    cd /home/$SAM
    rm -rf delete

    composer global require laravel/envoy
    /home/sam/.config/composer/vendor/bin/envoy run --conf Local.blade.php deploy --branch="$BRANCH" --password="$PASSWORD"
EOF

systemctl restart nginx
systemctl restart supervisor

# Certbot
snap install --classic certbot
certbot --nginx --non-interactive --agree-tos -m "4sam21@gmail.com" -d $HOSTNAME
systemctl restart nginx
systemctl restart supervisor

echo "DONE"
