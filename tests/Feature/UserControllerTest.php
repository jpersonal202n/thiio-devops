<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Foundation\Testing\WithFaker;
use Tests\TestCase;
use App\Models\User;
use Tests\Utilities\TestSetup;


class UserControllerTest extends TestCase
{

    use RefreshDatabase;

    public $testSetup;

    public function setUp(): void
    {
        parent::setUp();

        $this->testSetup = new TestSetup();
        $this->testSetup->initialize();

    }

    public function test_it_can_list_users(): void
    {
        User::factory()->count(10)->create();

        $response = $this->getJson('/');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    '*' => [
                        'id',
                        'name',
                        'email',
                    ],
                ],
                'message'
            ]);

        $responseData = $response->json('data');
        $firstUser = $responseData[0];

        $this->assertNotEmpty($responseData);
        $this->assertEquals(10, count($responseData)-1);
        $this->assertArrayHasKey('id', $firstUser);
        $this->assertArrayHasKey('name', $firstUser);
        $this->assertArrayHasKey('email', $firstUser);
    }

    public function test_it_cannot_store_a_user_without_token()
    {
        $data = [
            'name' => 'Test User',
            'last_name' => 'Test User',
            'email' => 'testuser2@example.com',
            'password' => 'secret-123',
            'password_confirmation' => 'secret-123'
        ];

        $response = $this->postJson('/api/v1/users', $data);

        $response->assertStatus(401)
            ->assertJson([
                'message' => 'Unauthenticated.'
            ]);
    }

    public function test_email_must_be_unique()
    {

        User::factory()->create([
            'email' => 'duplicate@example.com',
        ]);

        $data = [
            'name' => 'Test User',
            'email' => 'duplicate@example.com',
            'password' => 'password',
            'password_confirmation' => 'password',
        ];

        $response = $this->postJson('/api/v1/users', $data, [
            'Authorization' => 'Bearer ' . $this->testSetup->token
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('email');
    }

    public function test_password_must_be_between_6_and_16_characters()
    {

        $data = [
            'name' => 'Test User',
            'email' => 'shortpassword@example.com',
            'password' => 'short',
            'password_confirmation' => 'short',
        ];

        $response = $this->postJson('/api/v1/users', $data, [
            'Authorization' => 'Bearer ' . $this->testSetup->token
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('password');

        $data['password'] = $data['password_confirmation'] = str_repeat('a', 17);

        $response = $this->postJson('/api/v1/users', $data, [
            'Authorization' => 'Bearer ' . $this->testSetup->token
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('password');
    }

    public function test_password_confirmation_must_match()
    {
        $data = [
            'name' => 'Test User',
            'email' => 'passwordconfirmation@example.com',
            'password' => 'password',
            'password_confirmation' => 'differentpassword',
        ];

        $response = $this->postJson('/api/v1/users', $data, [
            'Authorization' => 'Bearer ' . $this->testSetup->token
        ]);

        $response->assertStatus(422)
                 ->assertJsonValidationErrors('password');
    }

    public function test_it_can_store_a_user()
    {
        $data = [
            'name' => 'Test User',
            'last_name' => 'Test User',
            'email' => 'testuser2@example.com',
            'password' => 'password',
            'password_confirmation' => 'password'
        ];

        $response = $this->postJson('/api/v1/users', $data, [
            'Authorization' => 'Bearer ' . $this->testSetup->token
        ]);

        $responseData = $response->json('data');

        $response->assertStatus(201)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
                'message'
            ]);

        $this->assertNotEmpty($responseData);
        $this->assertEquals('Test User', $responseData['name']);
        $this->assertEquals('testuser2@example.com', $responseData['email']);
    }

    public function test_it_can_show_a_user()
    {
        $user = User::factory()->create();

        $response = $this->getJson('/api/v1/users/' . $user->uuid, [
            'Authorization' => 'Bearer ' . $this->testSetup->token
        ]);

        $responseData = $response->json('data');

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'name',
                    'email',
                ],
                'message'
            ]);

        $this->assertNotEmpty($responseData);
        $this->assertEquals($user->uuid, $responseData['id']);
        $this->assertEquals($user->name, $responseData['name']);
        $this->assertEquals($user->email, $responseData['email']);
    }

    public function test_it_can_delete_a_user()
    {
        $user = User::factory()->create();

        $response = $this->deleteJson('/api/v1/users/' . $user->uuid, [], [
            'Authorization' => 'Bearer ' . $this->testSetup->token
        ]);

        $responseData = $response->json();

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'User removed succesfuly'
            ]);

        $this->assertEquals('User removed succesfuly', $responseData['message']);
        $this->assertNull(User::find($user->id));
    }

}
