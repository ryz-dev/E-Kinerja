<?php

use Illuminate\Database\Seeder;

class OauthClientSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $client_static = [
            'id' => 99,
            'name' => 'Client Dev',
            'secret' => 123456789,
            'personal_access_client' => 1,
            'password_client' => 1,
            'revoked' => 0,
            'redirect' => 'http://localhost'
        ];

        try {
            DB::table('oauth_clients')->insert($client_static);
        } catch (\Exception $e){
            return $e->getMessage();
        }
    }
}
