<?php

use Illuminate\Database\Seeder;

use App\Role;

class RoleTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        Role::truncate();
        
        $role_admin = Role::create([
            'name' => 'admin',
        ]);
        $role_artist = Role::create([
            'name' => 'artist',
        ]);
        $role_user = Role::create([
            'name' => 'user',
        ]);

        // attaching permissions to their roles
        foreach( $role_admin->available_permissions as $permission )  {
            $role_admin->current_permissions()->attach($permission->id);
        }
        foreach( $role_artist->available_permissions as $permission )  {
            $role_artist->current_permissions()->attach($permission->id);
        }
        foreach( $role_user->available_permissions as $permission )  {
            $role_user->current_permissions()->attach($permission->id);
        }
    }
}
