<?php

namespace App\Http\Controllers;

use App\Http\Requests\StorePhoto;
use App\Photo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class PhotoController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth')->except(['index', 'download','show']);
    }

    /**
     * 写真一覧
     */
    public function index()
    {
        $photos = Photo::with(['owner'])
            ->orderBy(Photo::CREATED_AT, 'desc')->paginate();

        return $photos;
    }

    public function show(string $id)
    {
        $photo = Photo::where('id', $id)->with(['owner'])->first();

        return $photo ?? abort(404);
    }

    public function download(Photo $photo)
    {
        if(! Storage::cloud()->exists($photo->filename)){
            abort(404);
        }

        $headers = [
            'Content-Type' => 'application/octet-stream',
            'Content-Disposition' => 'attachment; filename="' . $photo->filename . '"',
        ];

        return response(Storage::cloud()->get($photo->filename), 200, $headers);
    }

    /**
     * 写真投稿
     * @param StorePhoto $request
     * @return \Illuminate\Http\Request
     */
    public function create(StorePhoto $request)
    {
        $extension = $request->photo->extension();

        $photo = new Photo();

        $photo->filename = $photo->id . '.' . $extension;

        //s3にファイルを保存
        Storage::cloud()->putFileAs('', $request->photo, $photo->filename, 'public');

        DB::beginTransaction();

        try{
            Auth::user()->photos()->save($photo);
            DB::commit();
        } catch (\Exception $exception){
            DB::rollBack();

            Storage::cloud()->delete($photo->filename);

            throw $exception;
        }

        return response($photo, 201);
    }
}
