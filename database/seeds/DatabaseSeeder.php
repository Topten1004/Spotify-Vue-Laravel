<?php

use App\Setting;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     *
     * @return void
     */
    public function run()
    {
        $this->call(SettingTableSeeder::class);
        $this->call(PermissionTableSeeder::class);
        $this->call(RoleTableSeeder::class);
        $this->call(GenreTableSeeder::class);
        $this->call(PodcastGenreTableSeeder::class);
        $this->call(NavigationItemTableSeeder::class);
        $this->call(PageTableSeeder::class);
        $this->call(LanguageTableSeeder::class);
        $this->call(SectionTableSeeder::class);
        $this->call(PayoutMethodTableSeeder::class);
        $this->call(PlanTableSeeder::class);
    }
}
