# Installation on Mac Os

## 1. Install PHP

    curl -s http://php-osx.liip.ch/install.sh | bash -s 7.2


## 2. Install Laravel with Composer

    composer global require "laravel/installer"  

<img src="/assets/img/laravel_mac_os.png">

## 3. Edit bash profile

    vi ~/.bash_profile  

    export PATH=~/.composer/vendor/bin:$PATH  

    source ~/.bash_profile  

## 4. Deploy your app 

Extract your Muzzie zip file on the directory that you want


## 5. Start the server

    php artisan serve

<img src="/assets/img/localhost_mac_os.png">