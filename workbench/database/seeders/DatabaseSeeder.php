<?php

namespace Workbench\Database\Seeders;

use Cachet\Enums\ComponentStatusEnum;
use Cachet\Models\Component;
use Illuminate\Database\Seeder;
use Workbench\App\User;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        User::create([
            'name' => 'Cachet Demo',
            'email' => 'test@test.com',
            'password' => bcrypt('test123'),
            'email_verified_at' => now(),
            'active' => true,
        ]);

        Component::create([
            'name' => 'API',
            'status' => ComponentStatusEnum::operational,
        ]);
    }
}
