<?php

use Illuminate\Database\Seeder;
use App\User;
class UsersTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
    	$faker = Faker\Factory::create();

    	$limit = 10;
    	User::create([
    		'name'=>"Nguyễn Văn Hiệp",
    		'email'=>"Nguyenhiepvan.bka@gmail.com",
    		'password'=>\Hash::make('12345678')
    	]);
    	for ($i = 0; $i < $limit; $i++) {
    		User::create([
    			'name'=>$faker->name,
    			'email'=>$faker->email,
    			'password'=>\Hash::make('12345678')
    		]);
    	}
    }
}
