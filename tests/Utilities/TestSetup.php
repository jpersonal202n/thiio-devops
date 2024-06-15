<?php

namespace Tests\Utilities;

use App\Models\User;
use Laravel\Passport\ClientRepository;
use Illuminate\Support\Facades\Hash;

class TestSetup
{
    public $user;
    public $token;

    public function initialize()
    {

        $clientRepository = new ClientRepository();

        $clientRepository->createPersonalAccessClient(
            null, 'Test Personal Access Client', 'http://localhost'
        );

        $password = 'password';

        $this->user = User::factory()->create([
            'email' => 'testuser@example.com',
            'password' => Hash::make($password),
        ]);

        $this->token = $this->user->createToken('TestToken')->accessToken;
        
    }
}