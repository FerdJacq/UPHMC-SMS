<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Inertia\Inertia;

use Illuminate\Support\Carbon;
use App\Models\Seller;
use App\Models\SellersFile;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Response;
use Illuminate\Support\Facades\Storage;
use App\Library\Helper;
use Illuminate\Support\Str;
use Auth;
use Validator;
use DB;
use Excel;


class SellerSettingsController extends Controller
{
    private $controller = "SellerSettingsController";

    public function profile()
    {
        $seller = Seller::where('tin', '029203920132')->first();

        return Inertia::render('seller_settings/profile',["seller"=>$seller]);
    }

    public function updateProfile(Request $request)
    {

        $validator = Validator::make($request->all(), [
            'contact_number' => ["required"],
            'email' => ["required"],
        ]);


        $seller = Seller::where('tin', '029203920132')->first();


        $seller->update([
            'contact_number'=>$request->contact_number,
            'email'=>$request->email
        ]);
        return response()->json(['success' => 1,'data',"message"=>"Success!"]);
    }

    public function uploadFile(Request $request)
    {
        $seller = Seller::where('tin', '029203920132')->first();

        $validator = Validator::make($request->all(), [
            'file' => 'required|mimes:csv,txt,json,pdf,docx,docs,rtf,jpg,png,jpeg|max:10000000',
            'file_type' => 'required|string',
        ]);
        if($validator->fails()) 
        {
            $log = ['success' => 0, 'errors' => $validator->getMessageBag()->toArray()];
            return response()->json($log, 400);
        }

        $file = $request->file('file');
        $original_filename = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);

        $filename = Helper::ref_number("UF",20,"");
        $upload = new SellersFile;
        $upload->sellers_id = $seller->id;
        $upload->original_filename = $original_filename;
        $upload->filename = $filename;
        $upload->file_type = $request->file_type;
        $upload->mime_type = $file->getClientMimeType();
        $upload->extension = strtolower($file->getClientOriginalExtension());
        $upload->save();

        Log::info("Mime Type: " . $upload->mime_type);
        $filePath = $file->storeAs('private/sellers_files', $upload->id, 'local');

        Log::info('[Series/UploadFile]: File Name : '. $filename);
        return response()->json(['success' => 1,'data',"message"=>"Success!"]);

    }

    public function all(Request $request)
    {
        $search = $request->search;
        $start_date = $request->start_date;
        $end_date = $request->end_date;
        $file_type = $request->file_type;

        $seller = Seller::where('tin', '029203920132')->first();

        if ($seller->files->contains('file_type', 'Certificates')) $seller->update(['has_cor' => 1]);
        else $seller->update(['has_cor' => 0]);

        $data = SellersFile::where('sellers_id', $seller->id)
            ->when($search , function($query) use ($search)
            {
                $query->where("original_filename", 'LIKE', '%'. $search . '%');
            })
            ->when($file_type , function($query) use ($file_type)
            {
                $query->where("file_type", $file_type);
            })
            ->where(function($query) use ($start_date, $end_date)
            {
                $query->when($start_date || $end_date , function($query) use ($start_date, $end_date)
                {
                    $query->whereBetween('created_at', [$start_date, $end_date]);              
                });
            })
            ->orderBy('created_at')
            ->offset($request->page ?? 1)
            ->paginate($request->limit ?? 5);

        return response()->json(["status"=>"success","data"=>$data], 200);
    }

    public function downloadFile($id)
    {
        $file = SellersFile::find($id);
        if(!$file) return response()->json(["status" => 0, "message" => "File record does not exist."], 404);
        try{
            $file_path = "private/sellers_files/{$file->id}";
            $file_name = $file->original_filename;
            $headers = [
                'Content-Type' => $file->mime_type,
                'Content-Disposition' => "attachment; filename=$file_name",
            ];
            $file_content = Storage::disk()->get($file_path);
            if (Str::startsWith($file->mime_type, 'image/')) {
                $base64String = base64_encode($file_content);
                return Response::make($base64String, 200, $headers);
            }else {
                return Response::make($file_content, 200, $headers);
            } 
        }  
        catch(\Exception $e) {return response()->json(['status' => 0, 'message' => $e->getMessage()], 400);}
    }

    public function deleteFile($id)
    {
        $log_ref_no = Helper::generateRandomString(20);
        $function_name = "deleteFile";
        $file = SellersFile::find($id);
        if(!$file) return response()->json(["status" => 0, "message" => "File record does not exist."], 404);
        try 
        {
            $file->delete();
            Helper::logTransaction($this->controller, $function_name, $log_ref_no, "ID-$id : File deleted.");
            return response()->json(['status' => 1, 'message' => 'File deleted successfully!'], 200);
        }
        catch(\Exception $e) {return response()->json(['status' => 0, 'message' => $e->getMessage()], 400);}
    }
}
