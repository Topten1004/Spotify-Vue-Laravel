# Installation on a VPS machine

You can install Muzzie on any server machine.

The following installation is applied on a VPS machine with the operating system Ubuntu Server 20.04 LTS.


## 1. Set up the environment

<strong>1.1 Add php PPA (for Ubuntu only)</strong>

    sudo add-apt-repository ppa:ondrej/php


<strong>1.2 Update libraries </strong>

    sudo apt-get update

<strong>1.3  Install PHP with it's extenstions </strong>

    sudo apt-get install php7.4 openssl php7.4-common php7.4-curl php7.4-json php7.4-mbstring php7.4-mysql php7.4-xml php7.4-zip


## 2. Deploy your app 

<strong>2.1 Upload your Muzzie folder at the right place </strong>

You can use either an FTP (or SFTP) client or you can use git (much faster).

After extracting the given zip file, make sure your to upload the extracted folder under the path <code>/var/www/</code>

After the operation is successfully done, make sure you are on the directory of the project folder <code>/var/www/muzzie</code> (assuming here that your project folder is called 'muzzie' )


<strong>2.2  Grant permissions to folders </strong>

    sudo chmod 777 -R ./
    sudo chmod 777 -R storage/
    sudo chmod 777 -R resources/lang/
    sudo chmod 777 -R bootstrap/

## 3. Apache Configuration
Now we need to configure the Apache Server ( if you wanna use NGINX take a look at some guides out there )

<strong>3.1 Open the configuration file  </strong>

    sudo nano /etc/apache2/sites-available/000-default.conf

<strong>3.2  Add the following code under <code>DocumentRoot /var/www/html</code> </strong>

    DocumentRoot /var/www/muzzie/public
    <Directory "/var/www/muzzie/public">
        Options Indexes FollowSymLinks MultiViews
        AllowOverride All
        Order allow,deny
        allow from all
    </Directory>

## 4. Lunch your server

Activate the mod_rewrite module with

    sudo a2enmod rewrite

and restart the apache

    sudo service apache2 restart


## Finally
Check the URL of your app. by the moment, the app should be deployed and ready to be installed.

The integrated installer will take care of the rest (the installer should appear when you visit the base URL of your app).


<!-- ## Having difficulties deploying Muzzie?

If you have not managed to deploy the application yourself or you want someone else to take care of it. I would like to inform you that I'm available on [Fiverr](https://www.fiverr.com/abassion/deploy-and-run-your-laravel-application-on-a-vps-machine) to accomplish this job.

<strong>For Muzzie clients, i'm offring a special 50% discount</strong>.

[Deploy my laravel app](https://www.fiverr.com/abassion/deploy-and-run-your-laravel-application-on-a-vps-machine) -->
