<?php

namespace Tests\Feature\Api\V1;

use App\Models\User;
use Tests\TestCase;

class AuthTest extends TestCase
{
    /**
     * Test API V1 login success
     *
     * @return void
     */
    public function test_login_success()
    {
        $user = User::factory(['password' => bcrypt($this->defaultPassword)])->create();

        $response = $this->post('/auth/login', [
            'username' => $user->username,
            'password' => $this->defaultPassword,
        ]);

        $response->assertOk()
                 ->assertJson([
                     'status' => true,
                 ]);
    }

    /**
     * Test API V1 login failed
     *
     * @return void
     */
    public function test_login_failed()
    {
        $user = User::factory(['password' => bcrypt($this->defaultPassword)])->create();

        $response = $this->post('/auth/login', [
            'username' => $user->username,
            'password' => 'wrongpassword',
        ]);

        $response->assertUnauthorized();
    }

    /**
     * Test API V1 logout success
     *
     * @return void
     */
    public function test_logout_success()
    {
        $user = User::factory()->create();
        $token = $user->createToken('authToken')->plainTextToken;

        $response = $this->withHeaders([
            'Authorization' => 'Bearer '.$token,
        ])->post('/auth/logout', []);

        $response->assertOk();
    }

    /**
     * Test API V1 logout failed
     *
     * @return void
     */
    public function test_logout_failed()
    {
        $response = $this->post('/auth/logout', []);

        $response->assertUnauthorized();
    }
}
