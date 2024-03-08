<?php

use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\HelloController;
use App\Http\Controllers\DashboardController;
use Illuminate\Support\Facades\Route;
use Inertia\Inertia;

use App\Http\Controllers\FileController;

use App\Http\Controllers\ServiceProviderController;
use App\Http\Controllers\AccountController;
use App\Http\Controllers\EmailNotificationController;
use App\Http\Controllers\TransactionController;
use App\Http\Controllers\SellerController;
use App\Http\Controllers\WhitelistController;
use App\Http\Controllers\TwoFAController;
use App\Http\Controllers\CalendarController;
use App\Http\Controllers\SeriesController;
use App\Http\Controllers\SeriesCollectionController;
use App\Http\Controllers\SeriesUploadController;
use App\Http\Controllers\SummaryController;
use App\Http\Controllers\RegionController;
use App\Http\Controllers\SellerSettingsController;
/*
|--------------------------------------------------------------------------
| Web Routes
|--------------------------------------------------------------------------
|
| Here is where you can register web routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| contains the "web" middleware group. Now create something great!
|
*/


Route::get('/waybill', function () {
    return view('waybill');
});

Route::middleware(['auth'])->group(function () {
    Route::get('/auth/2fa', [TwoFAController::class, 'index'])->name('2fa.index');
    Route::post('/auth/validate', [TwoFAController::class, 'validateOTP'])->name('2fa.validate');
    Route::post('/auth/otp/resend', [TwoFAController::class, 'resend'])->name('2fa.resend');
});

Route::middleware(['auth','2fa'])->group(function () 
{  
    
    // Route::get('/', function () {
    //     return Inertia::render('welcome');
    // })->name('/');

    Route::get('/', [DashboardController::class, 'index'])->name('/');

    Route::get('dashboard', [DashboardController::class, 'index'])->name('dashboard');
    Route::resource('employee', EmployeeController::class)->only(['index', 'store', 'update', 'destroy']);

    Route::get('service-provider',[ServiceProviderController::class,'index'])->name('service-provider.index');
    Route::post('service-provider/create',[ServiceProviderController::class,'store'])->name('service-provider.store');
    Route::get('service-provider/all',[ServiceProviderController::class,'list'])->name('service-provider.list');
    Route::post('service-provider/validate',[ServiceProviderController::class,'validateRequest'])->name('service-provider.validate');
    Route::get('service-provider/data/{id}',[ServiceProviderController::class,'get'])->name('service-provider.get');
    Route::post('service-provider/update/{id}',[ServiceProviderController::class,'update'])->name('service-provider.update');
    Route::post('service-provider/delete/{id}',[ServiceProviderController::class,'destroy'])->name('service-provider.destroy');

    Route::get('region/list',[RegionController::class,'list'])->name('region.list');

    Route::get('profile',[AccountController::class,'profile'])->name('profile.index');
    Route::post('profile/update',[AccountController::class,'updateProfile'])->name('profile.update');
    
    Route::get('account',[AccountController::class,'index'])->name('account.index');
    Route::post('account/create',[AccountController::class,'store'])->name('account.store');
    Route::get('account/all',[AccountController::class,'list'])->name('account.list');
    Route::post('account/validate',[AccountController::class,'validateRequest'])->name('account.validate');
    Route::get('account/data/{id}',[AccountController::class,'get'])->name('account.get');
    Route::post('account/update/{id}',[AccountController::class,'update'])->name('account.update');
    Route::post('account/delete/{id}',[AccountController::class,'destroy'])->name('account.destroy');

    Route::get('transaction',[TransactionController::class,'index'])->name('transaction.index');
    Route::get('transaction/all',[TransactionController::class,'list'])->name('transaction.list');
    Route::get('transaction/data/{id}',[TransactionController::class,'get'])->name('transaction.get');
    Route::get('transaction/logs/{id}',[TransactionController::class,'seen'])->name('transaction.seen');
    Route::post('transaction/data/{id}',[TransactionController::class,'update'])->name('transaction.update');
    Route::post('transaction/generate',[TransactionController::class,'generate_report'])->name('transaction.generate_report');


    Route::get('seller',[SellerController::class,'index'])->name('seller.index');
    Route::get('seller/data/{id}',[SellerController::class,'get'])->name('seller.get');
    Route::get('seller/all',[SellerController::class,'list'])->name('seller.list');
    Route::get('seller/transactions',[SellerController::class,'transactions'])->name('seller.transactions');
    Route::post('seller/generate',[SellerController::class,'generate_report'])->name('seller.generate_report');
    Route::post('seller/generate/summary',[SellerController::class,'generate_summary_report'])->name('seller.generate_report_summary');


    Route::get('seller_settings',[SellerSettingsController::class,'profile'])->name('seller_settings');
    Route::post('seller_settings/update',[SellerSettingsController::class,'updateProfile'])->name('seller_settings.update');
    Route::post('seller_settings/file/upload',[SellerSettingsController::class,'uploadFile'])->name('seller_settings.file.upload');
    Route::get('seller_settings/file/all',[SellerSettingsController::class,'all'])->name('seller_settings.file.all');
    Route::get('seller_settings/file/{id}',[SellerSettingsController::class,'downloadFile'])->name('seller_settings.file.download_file');
    Route::post('seller_settings/file/delete/{id}',[SellerSettingsController::class,'deleteFile'])->name('seller_settings.file.delete_file');

    Route::post('dashboard/data',[DashboardController::class,'dashboard'])->name('dashboard.data');
    Route::get('dashboard/regions',[DashboardController::class,'regions'])->name('dashboard.region');
    Route::get('dashboard/seller',[DashboardController::class,'seller'])->name('dashboard.seller');
    
    Route::get('whitelist',[WhitelistController::class,'index'])->name('whitelist.index');
    Route::get('whitelist/data/{id}',[WhitelistController::class,'get'])->name('whitelist.get');
    Route::post('whitelist/create',[WhitelistController::class,'store'])->name('whitelist.store');
    Route::post('whitelist/update/{id}',[WhitelistController::class,'update'])->name('whitelist.update');
    Route::post('whitelist/delete/{id}',[WhitelistController::class,'destroy'])->name('whitelist.destroy');
    Route::get('whitelist/all',[WhitelistController::class,'list'])->name('whitelist.list');
    // Route::resource('whitelist', WhitelistController::class)->only(['index', 'store', 'destroy']);

    Route::get('calendar',[CalendarController::class,'index'])->name('calendar.index');
    Route::get('calendar/data',[CalendarController::class,'data'])->name('calendar.data');
    Route::get('calendar/regions',[CalendarController::class,'regions'])->name('calendar.regions');
    Route::get('calendar/export',[CalendarController::class,'export'])->name('calendar.export');

    Route::get('excel/{file_name}', [FileController::class, 'viewExcel']);

    Route::get('series', [SeriesController::class, 'index'])->name('series.index');
    Route::get('series/all',[SeriesController::class,'list'])->name('series.list');
    Route::post('series/create', [SeriesController::class, 'create'])->name('series.create');

    Route::get('series_collection', [SeriesCollectionController::class, 'index'])->name('series_collection.index');
    Route::get('series_collection/all',[SeriesCollectionController::class,'list'])->name('series_collection.list');
    Route::post('series_collection/create', [SeriesCollectionController::class, 'create'])->name('series_collection.create');

    Route::get('series_upload', [SeriesUploadController::class, 'index'])->name('series_upload.index');
    Route::post('series_upload/create', [SeriesUploadController::class, 'create'])->name('series_upload.create');
    Route::get('series_upload/all',[SeriesUploadController::class,'list'])->name('series_upload.list');
    Route::get('summary/counts',[SummaryController::class,'daily_count']);

    
});

Route::get('seller/all',[SellerController::class,'list'])->name('seller.list');
Route::get('/test_mail',[EmailNotificationController::class,'test_mail']);
Route::get('/image/{table}/{name}', [FileController::class, 'viewImage']);
Route::get('/file/{table}/{name}', [FileController::class, 'viewFileUpload']);

Route::get('electronic_receipt/{id}', [TransactionController::class, 'viewReceipt']);

require __DIR__.'/auth.php';
