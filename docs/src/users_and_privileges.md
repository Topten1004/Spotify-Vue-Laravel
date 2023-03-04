# Users & Privileges

Muzzie has 4 type of users. Each user has his own privileges and accessibility limits: 



## Guests 

A guest can: 

    show_playlist
    show_song
    show_album
    show_user
    show_podcast
    show_artist
    show_page
    show_genre
    show_podcast-genre
    play_song
    play_episode


## Users 
In addition to all guest privileges, a user can:

    edit_personal-infos
    request_artist-account
    add_friend
    remove_friend

    chat_with_friend
    block_friend
    unblock_friend
    
    like_song
    unlike_song
    like_album
    unlike_album
    follow_podcast
    unfollow_podcast
    follow_playlist
    unfollow_playlist
    follow_artist
    unfollow_artist

    create_playlist
    edit_playlist
    delete_playlist

    create_song
    delete_song ( own )
    edit_song_privacy (if permission is granted)
    download_song (if permission is granted)
    
    attach_song_to_playlist
    detach_song_to_playlist


## Artists
In addition to all previous privileges, an artist can: 

    show_analytics ( own )

    create_album ( if permission is granted )
    edit_album ( own ) ( if permission is granted )
    delete_album ( own ) ( if permission is granted )

    create_podcast ( if permission is granted ) 
    delete_podcast ( own ) ( if permission is granted )
    edit_podcast ( own ) ( if permission is granted )
        +includes
            create_episode
            edit_episode
            delete_episode



## Admins
In addition to all other privileges, an admin can:

    create_user ( if permission is granted )
    edit_user ( if permission is granted ) 
    delete_user ( if permission is granted )
    grant_roles & permissions to user ( if permission is granted ) 
    revoke_roles & permissions to user ( if permission is granted ) 

    create_artist ( if permission is granted ) 
    edit_artist ( if permission is granted )  
    delete_artist ( if permission is granted ) 

    create_genre ( if permission is granted ) 
    edit_genre ( if permission is granted )  
    delete_genre ( if permission is granted ) 

    create_podcast-genre ( if permission is granted ) 
    edit_podcast-genre ( if permission is granted )  
    delete_podcast-genre ( if permission is granted )

    create_page ( if permission is granted ) 
    edit_page ( if permission is granted )
    delete_page ( if permission is granted )

    edit_role ( if permission is granted ) 
        +includes
            add_permission
            remove_permission

    edit_setting ( if permission is granted )

    edit_appearance ( if permission is granted )
        +includes
            create_navigation-item
            edit_navigation-item
            delete_navigation-item