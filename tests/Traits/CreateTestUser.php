<?php
namespace Tests\Traits;

use App\User;
use Illuminate\Support\Facades\Hash;

trait CreateTestUser
{
    public function setUp(): void
    {
        $this->user = factory(User::class)->create(["password" => Hash::make("secret")]);
    }
}