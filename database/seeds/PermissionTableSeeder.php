<?php

use Illuminate\Database\Seeder;
use App\Permission;
class PermissionTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {

        Permission::truncate();

        // user permissions
        Permission::create([
            'name' => 'Upload songs',
            'role_id' => 3,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'Download songs',
            'role_id' => 3,
            'description' => 'download songs & podcast episodes.'
        ]);
        Permission::create([
            'name' => 'Chat with friends',
            'role_id' => 3,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'Listen with friends',
            'role_id' => 3,
            'description' => 'Listen with friends to the same song at the same time.'
        ]);
        Permission::create([
            'name' => 'Publish songs',
            'role_id' => 3,
            'description' => 'Ability to change songs privacy to public.'
        ]);
        Permission::create([
            'name' => 'Publish playlists',
            'role_id' => 3,
            'description' => 'Ability to change playlists privacy to public.'
        ]);
        Permission::create([
            'name' => 'No ads',
            'role_id' => 3,
            'description' => 'Does not see advertisements.'
        ]);


        // artist permissions
        Permission::create([
            'name' => 'CED songs(artist)',
            'role_id' => 2,
            'description' => 'Create, edit and delete songs.'
        ]);
        Permission::create([
            'name' => 'CED albums(artist)',
            'role_id' => 2,
            'description' => 'Create, edit and delete albums.'
        ]);
        Permission::create([
            'name' => 'CED podcasts(artist)',
            'role_id' => 2,
            'description' => 'Create, edit and delete podcasts.'
        ]);


        // admin permissions
        Permission::create([
            'name' => 'CED translations',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'contact',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'View sales',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'View payouts',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'Edit appearance',
            'role_id' => 1,
            'description' => 'Ability to change how the player interface appearance @admin/appearance.'
        ]);
        Permission::create([
            'name' => 'Edit settings',
            'role_id' => 1,
            'description' => 'Change the settings of the application @admin/settings.'
        ]);
        Permission::create([
            'name' => 'Edit advertisements',
            'role_id' => 1,
            'description' => 'Change the advertisements settings.'
        ]);
        
        Permission::create([
            'name' => 'CED pages',
            'role_id' => 1,
            'description' => ''
        ]);

        Permission::create([
            'name' => 'CED radio_stations',
            'role_id' => 1,
            'description' => ''
        ]);

        Permission::create([
            'name' => 'CED plans',
            'role_id' => 1,
            'description' => ''
        ]);

        Permission::create([
            'name' => 'CED subscriptions',
            'role_id' => 1,
            'description' => ''
        ]); 

        Permission::create([
            'name' => 'Create users',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'Edit users',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'Read users',
            'role_id' => 1,
            'description' => 'Can see the users data.'
        ]);
        Permission::create([
            'name' => 'Delete users',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'Create artists',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'Edit artists',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'Read artists',
            'role_id' => 1,
            'description' => 'Can see the artists data.'
        ]);
        Permission::create([
            'name' => 'Delete artists',
            'role_id' => 1,
            'description' => ''
        ]);
        Permission::create([
            'name' => 'CED songs',
            'role_id' => 1,
            'description' => 'Create, edit and delete songs.'
        ]);
        Permission::create([
            'name' => 'CED playlists',
            'role_id' => 1,
            'description' => 'Create, edit and delete playlists.'
        ]);
        Permission::create([
            'name' => 'CED song_genres',
            'role_id' => 1,
            'description' => 'Create, edit and delete song genres.'
        ]);
        Permission::create([
            'name' => 'CED podcast_genres',
            'role_id' => 1,
            'description' => 'Create, edit and delete podcast genres.'
        ]);
        Permission::create([
            'name' => 'CED radio stations',
            'role_id' => 1,
            'description' => 'Create, edit and delete radio stations.'
        ]);
        Permission::create([
            'name' => 'CED albums',
            'role_id' => 1,
            'description' => 'Create, edit and delete albums.'
        ]);
        Permission::create([
            'name' => 'CED podcasts',
            'role_id' => 1,
            'description' => 'Create, edit and delete podcasts.'
        ]);
        Permission::create([
            'name' => 'Read roles',
            'role_id' => 1,
            'description' => 'Can see the roles and their permissions.'
        ]);
        Permission::create([
            'name' => 'Edit roles',
            'role_id' => 1,
            'description' => 'Edit or Delete role permissions.'
        ]);
        Permission::create([
            'name' => 'Edit user roles',
            'role_id' => 1,
            'description' => 'Edit or Delete roles for a user.'
        ]);
        Permission::create([
            'name' => 'Edit user permissions',
            'role_id' => 1,
            'description' => 'Edit or Delete permissions for a user.'
        ]);
    }
}
