<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function show($filename)
    {
        if(auth()->guest()){
            return abort(403);
        }

        if(!Gate::allows('show-file')){
            abort(403);
        }
        // $path = Storage::path($filename);
        $path = storage_path('app/lampiran/'.$filename);
        $expiration = now()->addHours(1);
        $signedUrl = URL::temporarySignedRoute('show-file', $expiration,$filename);
        // dd($path);
        $file = Storage::disk('local')->get($path);
        // dd($file);
        $type       = Storage::mimeType($path);
        // return response($file, 200)->header('Content-Type', 'application/octet-stream');
        // return Response::make($file, 200, [
        //     'Content-Type'        => $type,
        //     'Content-Disposition' => 'inline; filename="'.$filename.'"'
        //   ]);
        return response()->download($path, null, [], null);
    }

}
