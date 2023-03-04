## Install Muzzie on CPanel

We recommend using a VPS machine. It is much faster than shared hosting since it improves the performance of your app.

To upload Muzzie to CPanel you need to:

## 1. Upload Muzzie

Using CPanel file manager, go to your <strong>public_html</strong>, Upload your Codecanyon zip file there and extract it.

<img src="/assets/img/upload & extract.png">

## 3. Update Permissions

Now change the permissions of the following folders to <strong>777</strong>:

        /storage/framework
        /storage/logs
        /resources/lang
        /bootstrap/cache

by right-clicking on them and choosing "change permissions"

<img src="/assets/img/permissions.png">

## 4. Check your domain

Now your app should be running at your domain. You should get an installer to help you configure your app and lunch it.