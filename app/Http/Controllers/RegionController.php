<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Region;

use Illuminate\Support\Facades\Log;
use Validator;
use Carbon;
use Helper;

class RegionController extends Controller
{
    public function list(Request $request)
    {
        $data = Region::get();   
        return response()->json(["status"=>"success","data"=>$data], 200);
    }
}
