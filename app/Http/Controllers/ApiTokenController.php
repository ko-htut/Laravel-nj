<?php

namespace App\Http\Controllers;
use Illuminate\Support\Str;
use App\ApiToken;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;
use Nahid\EnvatoPHP\Facades\Envato;


class ApiTokenController extends Controller
{


    public function index()
    {
    
        $settings = ApiToken::first();
       
        return response()->json($settings, 200);
    }


}