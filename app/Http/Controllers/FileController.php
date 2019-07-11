<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class FileController extends Controller
{
    public function upload(Request $request) {
        $data['path'] = $request->file('file')->store('uploads', 'public');
        $data['fullPath'] = asset('/storage/' . $data['path']);
        return $data;
    }
}
