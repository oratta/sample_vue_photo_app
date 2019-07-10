<?php

namespace Tests\Feature;

use App\User;
use Illuminate\Support\Facades\Hash;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class UserApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void {
        parent::setUp();

        $this->user = factory(User::class)->create(["password" => Hash::make("secret")]);
    }

    /**
     * @test
     */
    public function should_ログイン中のユーザを返却する()
    {
        $response = $this->actingAs($this->user)->json('GET', route('user'));

        $response
            ->assertStatus(200)
            ->assertJson([
                'name' => $this->user->name,
            ]);
    }

    /**
     * @test
     */
    public function should_未ログインの場合は空文字を返す()
    {
        $response = $this->json('GET', route('user'));

        $response->assertStatus(200);
        $this->assertEquals("", $response->content());
    }

}
