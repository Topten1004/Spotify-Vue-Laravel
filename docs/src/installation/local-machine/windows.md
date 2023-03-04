# Installation on your windows local machine


### 1. Extract and upload your file

Unzip the downloaded archive package. 

Upload the directory to your web server through FTP or Control Panel.

### 2. Create a database

Create a database for Muzzie (through your server control panel, phpMyAdmin, or any program of your choice).

### 3. Start your server 

#### If you are NOT using a server app ( Xampp, Wamp, Laragon, etc..)

<strong>a.</strong> Open your command line 

<strong>b.</strong> Go to the directory of your app

<strong>c.</strong> Start the server

        PHP artisan serve


#### If you are using a server app ( Xampp, Wamp, Laragon, etc..)

<strong>a.</strong> Make sure your app folder is in the right place ( each server program has its serving directory )

<strong>b.</strong> Start your server

::: danger
You should configure your web server's document / web root to be the <code>public</code> directory.
:::

### 3. Run the installation wizard

Go to your website address, then you'll see an installation wizard. just follow the easy steps and your app is good to go

<img src="/assets/img/muzzie_installer.png">