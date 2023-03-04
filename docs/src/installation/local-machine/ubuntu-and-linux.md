# Installation on ubuntu & linux

## 1. Set up the environment

<strong>1.1 Add php PPA (for Ubuntu only)</strong>

    sudo add-apt-repository ppa:ondrej/php


<strong>1.2 Update libraries </strong>

    sudo apt-get update

<strong>1.3  Install PHP with it's extenstions </strong>

    sudo apt-get install php7.4 openssl php7.4-common php7.4-curl php7.4-json php7.4-mbstring php7.4-mysql php7.4-xml php7.4-zip


## 2. Add your Muzzie files

extract your Muzzie zip file on the following directory <code>/var/www/</code>

After the operation is successfully done, make sure you are on the directory of the project folder <code>/var/www/muzzie</code> (assuming here that your project folder is called 'muzzie' )


<strong>2.2  Grant permissions to folders </strong>

    sudo chmod 777 /var/www/muzzie/;
    sudo chmod 777 -R storage/
    sudo chmod 777 -R public/
    sudo chmod 777 -R bootstrap/

## 3. Start your serve

    php artisan serve --host=[IP] --port=[port]