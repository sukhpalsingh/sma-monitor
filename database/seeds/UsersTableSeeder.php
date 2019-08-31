<?php

use App\User;
use Illuminate\Database\Seeder;

class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        User::truncate();
  
        User::create( [
            'email' => 'admin@sma-monitor.tk' ,
            'password' => Hash::make( 'monitor@sma2019' ) ,
            'name' => 'SMA Monitor' ,
        ] );
    }
}
