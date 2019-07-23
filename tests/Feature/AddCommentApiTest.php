<?php

namespace Tests\Feature;

use App\Photo;
use App\User;
use App\Comment;

use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;

class AddCommentApiTest extends TestCase
{
    use RefreshDatabase;

    public function setUp(): void
    {
        parent::setUp();

        $this->user = factory(User::class)->create();
    }

    /**
     * @test
     */
    public function should_コメントを追加できる()
    {
        factory(Photo::class)->create();
        $photo = Photo::first();

        $content = 'sample content';

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.comment',['photo'=>$photo->id,]), compact('content'));

        $comments = $photo->comments()->get();

        //レスポンスの内容一致
        $response->assertStatus(201)
            ->assertJsonFragment([
                "author" => [
                    "name" => $this->user->name,
                ],
                "content" => $content,
            ]);

        //DBにコメントが1件登録されていること
        $this->assertEquals(1, $comments->count());

        //内容一致
        $this->assertEquals($content, $comments[0]->content);
    }

}
