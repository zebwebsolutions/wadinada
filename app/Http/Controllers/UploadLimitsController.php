<?php

namespace App\Http\Controllers;

use Illuminate\Http\JsonResponse;

class UploadLimitsController extends Controller
{
    public function __invoke(): JsonResponse
    {
        return response()->json([
            'upload_max_filesize' => ini_get('upload_max_filesize'),
            'post_max_size' => ini_get('post_max_size'),
            'max_file_uploads' => ini_get('max_file_uploads'),
        ]);
    }
}
