<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $user1 = \App\Models\User::factory()->create([
            'name' => 'Usuário Teste',
            'email' => 'teste@teck.com',
            'password' => bcrypt('password'),
        ]);
        
        \App\Models\Wallet::create([
            'user_id' => $user1->id,
            'balance' => 1000.00
        ]);

        $user2 = \App\Models\User::factory()->create([
            'name' => 'Usuário Recebedor',
            'email' => 'recebedor@teck.com',
            'password' => bcrypt('password'),
        ]);

        \App\Models\Wallet::create([
            'user_id' => $user2->id,
            'balance' => 1000.00
        ]);
    }
}
