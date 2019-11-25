<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Importer\IndiwareImporter;

class UploadController extends Controller
{
    public function uploadFile(Request $request, $schoolId)
    {
        if ($request->hasFile('file')) {

            $import = new IndiwareImporter($schoolId, $request->get('user')->manager->user_id);
            $response = $import->import($request->file('file'));

            $response['message'] = 'File upload successful.';

            return response($response, 200);
        } else {
            //TODO: fix misleading status code -> xml request key
            return response(['message' => 'Wrong file format provided.'], 406);
        }
    }
}
