<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

use App\Models\Series;
use App\Models\SeriesCollection;

use Illuminate\Support\Facades\Log;
use App\Http\Controllers\EmailNotificationController;
use Illuminate\Support\Carbon;
use DB;

class SeriesGenerate extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'series:generate';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = '';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $data = SeriesCollection::where("status","PENDING")
        ->whereNull("processed_at")->get();

        foreach ($data as $item) {
            $start = $item->starting_no;
            $end = $item->ending_no;
            $leading_length = $item->length;
            $array = range($start, $end);
            
            $objects = array_map(function($value) use ($item,$leading_length) {
                $series_no = str_pad($value, $leading_length, '0', STR_PAD_LEFT);
                $complete_no = $item->company_code . $item->prefix . $series_no . $item->suffix;
                return  [
                    'service_provider_id'=>$item->service_provider_id,
                    'company_code'=>$item->company_code,
                    'prefix'=>$item->prefix,
                    'suffix'=>$item->suffix,
                    'series_no'=>$series_no,
                    'complete_no'=>$complete_no
                ];
            }, $array);

                        
            DB::beginTransaction();
            $total_failed = 0;
            $total_inserted = 0;
            $start_time =  Carbon::now();
            $item->processed_at = $start_time; 
            $item->status = "PROCESSING";
            $item->save();
            try {
                $total_inserted = DB::table('series')->insertOrIgnore($objects);
                $total_failed = count($objects) - $total_inserted;
                DB::commit();
            } catch (\Throwable $e) {
                DB::rollback();
            }
            $end_time =  Carbon::now();
            $item->completed_at = $end_time; 
            $item->total_success = $total_inserted;
            $item->total_failed = $total_failed;  
            $item->status = "COMPLETED";
            $item->save();

            // Series::insertOrIgnore($objects);
            // echo json_encode($objects);
        }
    }
}
