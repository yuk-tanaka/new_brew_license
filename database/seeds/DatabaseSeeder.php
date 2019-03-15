<?php

use App\Eloquents\NotifiedUser;
use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        NotifiedUser::query()->create([
            'line_token' => env('LINE_NOTIFICATION_TOKEN'),
        ]);
    }
}
