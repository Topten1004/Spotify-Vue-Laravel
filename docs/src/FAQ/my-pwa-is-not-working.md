# How to set up PWA correcty?

After the installation of your app, a manifest file will be auto-generated with your site URL. This manifest file is the resposible for gettings your PWA work. Maybe you chage your URl later and the manifest file does no longer take effect. In this small guide we will show you how to setup the manifest file correctly and make your PWA work with no problems. 

Here is the default manifest.json file structure

    {
        "name": "SITENAME",
        "short_name": "SHORT_SITENAME",
        "theme_color": "#4245a8",
        "background_color": "#ffffff",
        "display": "standalone",
        "scope": "http://example.com/",
        "start_url": "http://example.com/home",
        "icons": [
            {
            "src": "/images/favicon/icon-192x192.png",
            "sizes": "192x192",
            "type": "image/png"
            },
            {
            "src": "/images/favicon/icon-512x512.png",
            "sizes": "512x512",
            "type": "image/png"
            },
            {
            "src": "/images/favicon/maskable_icon_x192.png",
            "sizes": "192x192",
            "type": "image/png",
            "purpose": "maskable"
            },
            {
            "src": "/images/favicon/maskable_icon_x512.png",
            "sizes": "512x512",
            "type": "image/png",
            "purpose": "maskable"
            }
        ]
    }

1. First step is to make sure that your URL is correct. If it is not correct than your PWA won't work.

2. Select the theme & background color (optional).

3. Update the icons files:

As you can see there are 4 icons which will represent your app after the installation on all devices. Go to the following path public/images/favicon and update the images with yours. Make sure your respect the sizes mentioned above.


Your PWA should be working by now. If this not the case, please feel free to contact us and we will get back to you soon.
