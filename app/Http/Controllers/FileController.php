<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\File;

use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Response;

use App\Models\FileUpload;

class FileController extends Controller
{
    public static function saveImage($id, $folder_name, $base64)
    {
        try
        {
            $replace = substr($base64, 0, strpos($base64, ',') + 1);
            $file = str_replace($replace, '', $base64); 
            $file = str_replace(' ', '+', $file); 
            $file = base64_decode($file);

            // $extension = explode('/', explode(':', substr($base64, 0, strpos($base64, ';')))[1])[1];

            $file_info = finfo_open();
            $mime_type = finfo_buffer($file_info, $file, FILEINFO_MIME_TYPE);
            $extension = 'png';

            Storage::disk()->put("private/$folder_name/$id.$extension", $file);
            // Log::info("private/$folder_name/$id.$extension");
        }
        catch(\Exception $ex)
        {
            Log::error("[UPLOAD_IMAGE][$folder_name][$id]");
            Log::error($ex);
        }
    }

    public static function saveVideo($id, $folder_name, $base64)
    {
        try
        {
            // Extract the base64 part and decode it
            $replace = substr($base64, 0, strpos($base64, ',') + 1);
            $file = str_replace($replace, '', $base64); 
            $file = str_replace(' ', '+', $file); 
            $file = base64_decode($file);

            // Determine the file extension from the base64 string
            $extension = explode('/', explode(':', substr($base64, 0, strpos($base64, ';')))[1])[1];

            // Get the MIME type
            $file_info = finfo_open();
            $mime_type = finfo_buffer($file_info, $file, FILEINFO_MIME_TYPE);
            
            // Check for valid video extension (optional)
            $valid_extensions = ['mp4', 'avi', 'mov', 'wmv', 'mkv'];
            if (!in_array($extension, $valid_extensions)) {
                throw new \Exception('Invalid video format.');
            }

            // Store the video file
            Storage::disk()->put("private/$folder_name/$id.$extension", $file);

            Log::info("private/$folder_name/$id.$extension");
        }
        catch (\Exception $ex)
        {
            Log::error("[upload_video]");
            Log::error($ex);
        }
    }

    public static function fileUpload($file_name, $id, $table , $file, $is_base64=true)
    {
        try
        {
            $size = 0;
            $mime_type = "";
            $extension = "";
            $nfile = "";

            if($is_base64)
            {
                $replace = substr($file, 0, strpos($file, ',')+1);
                $nfile = str_replace($replace, '', $file); 
                $nfile = str_replace(' ', '+', $nfile); 
                $nfile = base64_decode($nfile);
    
                $extension = explode('/', explode(':', substr($file, 0, strpos($file, ';')))[1])[1];
    
                $f = finfo_open();
                $mime_type = finfo_buffer($f, $nfile, FILEINFO_MIME_TYPE);
                $size = static::getBase64ImageSize($file);
            }
            else
            {
                $size = (int) $file->getSize();
                $extension = strtolower($file->getClientOriginalExtension());
            }

            if($extension == 'octet-stream')
            {
                $extension = 'jpg';
            }
            else if($mime_type == "image/png")
            {
                $mime_type = "image/jpeg";
                $extension = 'jpg';
            }
            else if($mime_type == "image/jpeg")
            {
                $mime_type = "image/jpeg";
                $extension = 'jpg';
            }
            else
            {
                $extension = $extension;
            }

            $data = FileUpload::updateOrCreate(
                ['table' => $table, 'table_id' => $id],
                [
                    'name' => $file_name,
                    'size' => $size,
                    'mime_type' => $mime_type,
                    'extension' => $extension
                ]
            );

            Storage::disk()->put("private/$table/$id.$data->extension", $nfile);
        }
        catch(\Exception $ex)
        {
            Log::error($ex);
        }
    }

    public function viewImage($folder, $file_id, Request $request)
    {
        $secure = $request->secure;
        $file_path_png = "private/$folder/$file_id.png";
        $file_path_mp4 = "private/$folder/$file_id.mp4";

        $exists_png = Storage::disk()->exists($file_path_png);
        $exists_mp4 = Storage::disk()->exists($file_path_mp4);

        if (!$exists_png && !$exists_mp4) 
        {
            // Uncomment and customize these lines based on your gender logic
            // if (strtolower($request->gender) == "male") {
            //     return response()->file("images/male.jpg");
            // } else if (strtolower($request->gender) == "female") {
            //     return response()->file("images/female.jpg");
            // } else if ($folder == "accounts") {
            //     return response()->file("images/male.jpg");
            // }
            $file_name = 'default.png';
            if ($folder == "accounts") $file_name = 'default-user.jpg';

            return response()->file("images/$file_name");
        }

        // Determine which file to serve
        if ($exists_png) 
        {
            $file = Storage::disk()->get($file_path_png);
            $type = Storage::mimeType($file_path_png);
        } 
        else 
        {
            $file = Storage::disk()->get($file_path_mp4);
            $type = Storage::mimeType($file_path_mp4);
        }

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }


    public function viewFileUpload(Request $request,$table, $name)
    {
        $file = FileUpload::where("table", $table)
            ->where('name', $name)
            ->first();
        
        if(!$file)
        {
            return response()->file("images/default.png");
        }

        $filename = $file->name .".". $file->extension;
        $file_path = "$table/$filename";

        $exists = Storage::disk('private')->exists($file_path);

        if(!$exists)
        {
            $file_path = public_path("images/default.png");
        }
        else
        {
            $file_path = storage_path() ."/app/private/$file_path";
        }

        $original_name = str_replace(',', '', $file->original_name);

        return response()->file($file_path,
        [
            'Content-Type' => $file->extension,
            'Content-Disposition' => "inline; filename=$original_name.$file->extension"
        ]);
    }

    private static function getBase64ImageSize($base64Image) // return memory size in B, KB, MB
    {
        try
        {
            $size_in_bytes = (int) (strlen(rtrim($base64Image, '=')) * 3 / 4);
            $size_in_kb = $size_in_bytes / 1024;
            $size_in_mb = $size_in_kb / 1024;
    
            return $size_in_mb;
        }
        catch(Exception $e)
        {
            return $e;
        }
    }

    public function viewExcel($file_type,$date, $file_name)
    {
        $file_path = "$file_type/$date/$file_name";
        $exists = Storage::disk()->exists($file_path);

        if(!$exists) abort(404);

        $file = Storage::disk()->get($file_path);
        $type = Storage::mimeType($file_path);

        $response = Response::make($file, 200);
        $response->header("Content-Type", $type);
        return $response;
    }
}