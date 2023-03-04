# Users & Privileges

Muzzie has 4 types of users. Each user has his privileges and accessibility limits: 



## Guests 

Guests are visitors to your application with no authentication. Guests can by default: 

<ul-comp :items="[
        'See & play playlists',
        'See & play songs',
        'See & play albums',
        'See users',
        'See & play podcasts',
        'See artists',
        'See pages',
        'See genres',
        'See podcast-genres'
    ]">
</ul-comp>


## Users 
Users are the ones who has accounts in your app. In addition to all guest privileges, a users can:

<ul-comp :items="[
    'Edit there personal informations',
    'Request an artist account (if permission granted)',
    'Add & remove friends',
    'Like & unlike songs',
    'Like & unlike albums',
    'Follow & unfollows podcasts',
    'Follow & unfollows playlists',
    'Follow & unfollows artists',
    'Create, edit, and delete playlists',
    'Create, delete, and download their songs',
    'Edit their songs privacy (if permission granted)'
]">
</ul-comp>

## Artists

In addition to all guests and users privileges, an artist can: 
<ul-comp :items="[
    'See their stats & analytics',
    'Create, edit, and manage their albums (if permission is granted)',
    'Create, edit, and manage their podcasts (if permission is granted)'
]">
</ul-comp>


## Admins

You can grant the admin role to people to help you manage your application. Meantime you can restrict what they can do by adjusting their permissions. By default an admin can:

<ul-comp :items="[
    'Create, edit, and delete users (if permission is granted)',
    'Create, edit, and delete artists (if permission is granted)',
    'Create, edit, and delete genres (if permission is granted)',
    'Create, edit, and delete podcast genres (if permission is granted)',
    'Create, edit, and delete pages (if permission is granted)',
    'Create, edit, and delete permissions (if permission is granted)',
    'Manage other users permissions (if permission granted)',
    'Adjust the app settings (if permission granted)',
    'Adjust the app appearances (if permission granted)'
]">
</ul-comp>