<?php
  
use Carbon\Carbon;
use Illuminate\Support\Str;
/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('convertYmdToMdy')) {
    function convertYmdToMdy($date)
    {
        return Carbon::createFromFormat('Y-m-d', $date)->format('m-d-Y');
    }
}

if (! function_exists('gen_random_string')) {
    function gen_random_string($length)
    {
       return Str::random($length);
    }
}

if (! function_exists('gen_dsp_code')) {
    function gen_dsp_code()
    {
      return "DSP".strtoupper(gen_random_string(4));
    }
}
  
/**
 * Write code on Method
 *
 * @return response()
 */
if (! function_exists('convertMdyToYmd')) {
    function convertMdyToYmd($date)
    {
        return Carbon::createFromFormat('m-d-Y', $date)->format('Y-m-d');
    }
}

if (! function_exists('limitDecimal')) {
    function limitDecimal($amount,$decimal)
    {
        return sprintf("%0.".$decimal."f",$amount);
    }
}

if (! function_exists('ReadableFileSize')) {
    function ReadableFileSize($size, $precision = 2)
    {
        if ( $size > 0 ) {
            $size = (int) $size;
            $base = log($size) / log(1024);
            $suffixes = array(' bytes', ' KB', ' MB', ' GB', ' TB');
            return round(pow(1024, $base - floor($base)), $precision) . $suffixes[floor($base)];
        }

        return $size;
    }
}
