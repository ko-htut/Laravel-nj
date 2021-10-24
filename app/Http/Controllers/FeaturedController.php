<?php

namespace App\Http\Controllers;

use App\Http\Requests\FeaturedRequest;
use App\Http\Requests\FeaturedUpdateRequest;
use App\Http\Requests\StoreImageRequest;
use App\Jobs\SendNotification;
use App\Featured;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Carbon;
use Illuminate\Support\Facades\Storage;


class FeaturedController extends Controller
{
    

// returns all upcoming for api
    public function index()
    {
        return response()->json(Featured::orderByDesc('id')->paginate(12), 200);
    }


    public function latest()
    {

        $streaming = Featured::orderByDesc('created_at')
            ->limit(10)
            ->get();

        return response()
            ->json(['upcoming' => $streaming], 200);
    }


    // returns all upcoming for admin panel
    public function data()
    {
        return response()->json(Featured::orderByDesc('created_at')
        ->get(), 200);
    }

    // create a new upcoming in the database
    public function store(FeaturedRequest $request)
    {
        if (isset($request->featured)) {

            $featured = new Featured();
            $featured->fill($request->featured);
            $featured->save();
            $data = [
                'status' => 200,
                'message' => 'successfully created',
                'body' => $featured
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be created',
            ];
        }


        return response()->json($data, $data['status']);
    }

    // returns a specific upcoming
    public function show(Featured $featured)
    {
        return response()->json($featured, 200);
    }

    // update a upcoming in the database
    public function update(FeaturedUpdateRequest $request, Featured $featured)
    {
        if ($featured != null) {

            $featured->fill($request->featured);
            $featured->save();
            $data = [
                'status' => 200,
                'message' => 'successfully updated',
                'body' => $featured
            ];
        } else {
            $data = [
                'status' => 400,
                'message' => 'could not be updated',
            ];
        }

        return response()->json($data, $data['status']);
    }

    // delete a upcoming in the database
    public function destroy($featured)
    {
        if ($featured != null) {

            Featured::find($featured)->delete();

            $data = ['status' => 200, 'message' => 'successfully deleted',];
        } else {
            $data = ['status' => 400, 'message' => 'could not be deleted',];
        }

        return response()->json($data, 200);

    
    }

    // save a new image in the upcoming folder of the storage
    public function storeImg(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            $filename = Storage::disk('featured')->put('', $request->image);
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/featured/image/' . $filename,
                'message' => 'successfully uploaded'
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }

    // return an image from the upcoming folder of the storage
    public function getImg($filename)
    {

        $image = Storage::disk('featured')->get($filename);

        $mime = Storage::disk('featured')->mimeType($filename);

        return (new Response($image, 200))
            ->header('Content-Type', $mime);
    }


}

