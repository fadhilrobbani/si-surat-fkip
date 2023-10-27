<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Storage;

class FileController extends Controller
{
    // public function show($path)
    // {
    //     $file = Storage::disk('local')->get($path);
    //     $type       = Storage::mimeType($path);
    //     $fileName   = Storage::name($path);
    //     // return response($file, 200)->header('Content-Type', 'application/octet-stream');
    //     return Response::make($file, 200, [
    //         'Content-Type'        => $type,
    //         'Content-Disposition' => 'inline; filename="'.$fileName.'"'
    //       ]);
    // }
    public function show($path){
        if(auth()->check()){
            return redirect('storage/'.$path);
        }

    }
}
