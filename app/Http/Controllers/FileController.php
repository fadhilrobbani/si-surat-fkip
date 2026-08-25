<?php

namespace App\Http\Controllers;

use App\Models\Surat;
use App\Services\StorageHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\URL;
use Illuminate\Support\Facades\Gate;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

class FileController extends Controller
{
    public function show(Surat $surat, $filename)
    {
        if (auth()->guest()) {
            return abort(403);
        }

        // Try cloud storage (R2) first, then fallback to local
        return StorageHelper::response('lampiran/' . $filename);
    }
}
