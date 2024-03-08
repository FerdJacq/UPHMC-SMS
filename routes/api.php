<?php

use App\Http\Controllers\Api\EmployeeController;
use App\Http\Controllers\Api\ServiceProviderController;
use App\Http\Controllers\Api\TransactionController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider within a group which
| is assigned the "api" middleware group. Enjoy building your API!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});
Route::prefix('v1')->group(function () {
    Route::get('employee', [EmployeeController::class, 'index']);
    Route::get('service-provider', [ServiceProviderController::class, 'index']);
});

Route::middleware(['whitelistIP'])->group(function () {
    Route::get('transaction/verify/{search}', [TransactionController::class, 'verify']);
    Route::post('transaction', [TransactionController::class, 'save']);
    Route::post('transaction/status', [TransactionController::class, 'update']);
    Route::post('transaction/remitted', [TransactionController::class, 'set_remitted']);
});

Route::get('transaction/blockchain', [TransactionController::class, 'insert_blockchain']);

Route::get('qr_set_status', [TransactionController::class, 'qr_set_status']);