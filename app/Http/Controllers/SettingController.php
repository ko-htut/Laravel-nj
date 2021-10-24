<?php

namespace App\Http\Controllers;


use App\Http\Requests\LogoRequest;
use App\Http\Requests\SettingsRequest;
use App\Http\Requests\StoreImageRequest;
use App\Setting;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Nahid\EnvatoPHP\Facades\Envato;
use Illuminate\Support\Str;

class SettingController extends Controller
{




    // return the settings by hiding the sensitive fields for the api
    public function index()
    {


    
        $settings = Setting::first()
            ->makeVisible([
                'authorization'
            ])->toArray();

        return response()->json($settings, 200);
    }


    public function app($deviceId)
    {

        $newToken =  Str::random(128);
        create([
            'service' => $service,
            'token' => bcrypt($newToken),
            'deviceId' => $deviceId

        ]);


        return response()->json($newToken, 200);
    }


    // return all settings for the admin panel
    public function data()
    {
        return response()->json(Setting::first());
    }

    // update the settings in the database
    public function update(SettingsRequest $request, Setting $setting)
    {
        $setting->update($request->all());
        $data = [
            'status' => 200,
            'message' => 'successfully updated',
            'body' => $setting
        ];

        return response()->json($data, $data['status']);
    }

    // update the logo in the storage, deleting the folder and creating it again to ensure that there is only one file either PNG or SVG
    public function updateLogo(LogoRequest $request)
    {
        if ($request->hasFile('image')) {
            Storage::disk('public')->deleteDirectory('logo');
            $extension = $request->image->getClientOriginalExtension();
            $filename = Storage::disk('public')->putFileAs('logo', $request->image, "logo.$extension");
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/image/logo?' . time(),
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }


    
    public function customBanner(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            Storage::disk('images')->deleteDirectory('custombanner');
            $extension = $request->image->getClientOriginalExtension();
            $filename = Storage::disk('images')->putFileAs('custombanner', $request->image, "custombanner.$extension");
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/image/custombanner',
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }


    public function updateSplash(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            Storage::disk('images')->deleteDirectory('splash');
            $extension = $request->image->getClientOriginalExtension();
            $filename = Storage::disk('images')->putFileAs('splash', $request->image, "splash.$extension");
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/image/splash',
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }



    public function updateEpisode(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            Storage::disk('images')->deleteDirectory('episode');
            $extension = $request->image->getClientOriginalExtension();
            $filename = Storage::disk('images')->putFileAs('episode', $request->image, "episode.$extension");
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/image/episode',
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }


    public function mediahome(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            Storage::disk('public')->deleteDirectory('mediahome');
            $extension = $request->image->getClientOriginalExtension();
            $filename = Storage::disk('public')->putFileAs('mediahome', $request->image, "mediahome.$extension");
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/image/mediahome',
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }


    public function storeImg(StoreImageRequest $request)
    {
        if ($request->hasFile('image')) {
            $filename = Storage::disk('public')->put('', $request->image);
            $data = [
                'status' => 200,
                'image_path' => $request->root() . '/api/public/image/' . $filename,
                'message' => 'successfully uploaded'
            ];
        } else {
            $data = [
                'status' => 400,
            ];
        }

        return response()->json($data, $data['status']);
    }


    public function updateMiniLogo(LogoRequest $request)
    {
        if ($request->hasFile('image')) {
            Storage::disk('public')->deleteDirectory('miniLogo');
            $extension = $request->image->getClientOriginalExtension();
            $filename = Storage::disk('public')->putFileAs('miniLogo', $request->image, "miniLogo.$extension");
            $data = [
                'status' => 'success',
                'image_path' => $request->root() . '/api/image/minilogo?' . time(),
            ];
        } else {
            $data = [
                'status' => 'error',
            ];
        }

        return response()->json($data, 200);
    }

    // return the logo checking the format
    public function showLogo()
    {
        if (Storage::disk('public')->exists('logo/logo.svg')) {
            $image = Storage::disk('public')->get('logo/logo.svg');
            $mime = Storage::disk('public')->mimeType('/logo/logo.svg');
            $type = 'svg';
        } else {
            $image = Storage::disk('public')->get('logo/logo.png');
            $mime = Storage::disk('public')->mimeType('logo/logo.png');
            $type = 'png';
        }
        return (new Response($image, 200))
            ->header('Content-Type', $mime)->header('type', $type);
    }


    
    public function showSplash()
    {
        if (Storage::disk('images')->exists('splash/splash.svg')) {
            $image = Storage::disk('images')->get('splash/splash.svg');
            $mime = Storage::disk('images')->mimeType('/splash/splash.svg');
            $type = 'svg';
        } else if(Storage::disk('images')->exists('splash/splash.png')) {
            $image = Storage::disk('images')->get('splash/splash.png');
            $mime = Storage::disk('images')->mimeType('splash/splash.png');
            $type = 'png';
        } else if(Storage::disk('images')->exists('splash/splash.jpg')) {
            $image = Storage::disk('images')->get('splash/splash.jpg');
            $mime = Storage::disk('images')->mimeType('splash/splash.jpg');
            $type = 'jpg';
        }
        return (new Response($image, 200))
            ->header('Content-Type', $mime)->header('type', $type);
    }

    public function showEpisode()
    {
        if (Storage::disk('images')->exists('episode/episode.svg')) {
            $image = Storage::disk('images')->get('episode/episode.svg');
            $mime = Storage::disk('images')->mimeType('/episode/episode.svg');
            $type = 'svg';
        } else if(Storage::disk('images')->exists('episode/episode.png')) {
            $image = Storage::disk('images')->get('episode/episode.png');
            $mime = Storage::disk('images')->mimeType('episode/episode.png');
            $type = 'png';
        } else if(Storage::disk('images')->exists('episode/episode.jpg')) {
            $image = Storage::disk('images')->get('episode/episode.jpg');
            $mime = Storage::disk('images')->mimeType('episode/episode.jpg');
            $type = 'jpg';
        }
        return (new Response($image, 200))
            ->header('Content-Type', $mime)->header('type', $type);
    }


    public function showcustomBanner()
    {
        if (Storage::disk('images')->exists('custombanner/custombanner.svg')) {
            $image = Storage::disk('images')->get('custombanner/custombanner.svg');
            $mime = Storage::disk('images')->mimeType('/custombanner/custombanner.svg');
            $type = 'svg';
        } else if(Storage::disk('images')->exists('custombanner/custombanner.png')) {
            $image = Storage::disk('images')->get('custombanner/custombanner.png');
            $mime = Storage::disk('images')->mimeType('custombanner/custombanner.png');
            $type = 'png';
        } else if(Storage::disk('images')->exists('custombanner/custombanner.jpg')) {
            $image = Storage::disk('images')->get('custombanner/custombanner.jpg');
            $mime = Storage::disk('images')->mimeType('custombanner/custombanner.jpg');
            $type = 'jpg';
        }
        return (new Response($image, 200))
            ->header('Content-Type', $mime)->header('type', $type);
    }



    public function getImg($filename)
    {

        $image = Storage::disk('public')->get($filename);

        $mime = Storage::disk('public')->mimeType($filename);

        return (new Response($image, 200))
            ->header('Content-Type', $mime);
    }

    // return the mini logo checking the format
    public function showMiniLogo()
    {
        if (Storage::disk('public')->exists('miniLogo/miniLogo.svg')) {
            $image = Storage::disk('public')->get('miniLogo/miniLogo.svg');
            $mime = Storage::disk('public')->mimeType('/miniLogo/miniLogo.svg');
            $type = 'svg';
        } else {
            $image = Storage::disk('public')->get('miniLogo/miniLogo.png');
            $mime = Storage::disk('public')->mimeType('miniLogo/miniLogo.png');
            $type = 'png';
        }
        return (new Response($image, 200))
            ->header('Content-Type', $mime)->header('type', $type);
    }





}
