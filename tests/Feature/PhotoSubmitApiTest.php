<?php

namespace Tests\Feature;

use Illuminate\Http\UploadedFile;
use Tests\TestCase;
use Illuminate\Foundation\Testing\WithFaker;
use Illuminate\Foundation\Testing\RefreshDatabase;
use App\Photo;
use App\User;
use Tests\Traits\CreateTestUser;

class PhotoSubmitApiTest extends TestCase
{
    use RefreshDatabase;
    use CreateTestUser;

    /**
     * @test
     */
    public function should_ファイルをアップロードできる()
    {
        //テスト用のストレージ
        // -> storage/framework/testing
        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'),
                [
                'photo' => UploadedFile::fake()->image('photo.jpg'),
                ]);

        $response->assertStatus(201);
        $photo = Photo::first();

        // 写真のIDが12桁のランダムな文字列であること
        $this->assertRegExp('/^[0-9a-zA-Z-_]{12}$/', $photo->id);

        // DBに挿入されたファイル名のファイルがストレージに保存されていること
        Strage::cloud()->assertExists($photo->filename);
    }

    /**
     * @test
     */
    public function should_データベースエラーの場合はs3にファイルを保存しない()
    {
        //強制エラー
        Schema::drop('photos');

        Storage::fake('s3');

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'),
                [
                   'photo' => UploadedFile::fake()->image('photo.jpg'),
                ]);

        $response->assertStatus(500);

        $this->assertEquals(0, count(Strage::cloud()->files()));

    }

    /**
     * @test
     */
    public function should_s3ファイル保存でエラーの場合データベースに保存しない()
    {
        Strage::shouldReceive('cloud')
            ->once()
            ->andReturnNull();

        $response = $this->actingAs($this->user)
            ->json('POST', route('photo.create'),
                [
                    'photo' => UploadedFile::fake()->image('photo.jpg'),
                ]);

        $response->assertStatus(500);

        $this->assertEmpty(Photo::all());
    }


}
