<?php

use App\Http\Controllers\ClientController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\LoginController;
use App\Http\Controllers\SalepersonController;
use App\Http\Controllers\SalesController;
use App\Http\Controllers\SalesmanagerController;
use App\Http\Controllers\UserController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
|
| Here is where you can register API routes for your application. These
| routes are loaded by the RouteServiceProvider and all of them will
| be assigned to the "api" middleware group. Make something great!
|
*/

Route::middleware('auth:sanctum')->get('/user', function (Request $request) {
    return $request->user();
});

Route::post('/login', [LoginController::class, 'login']);

Route::post('/updateuser', [UserController::class, 'update']);
Route::post('/updatePassword', [UserController::class, 'updatePassword']);
Route::post('/getallusers', [UserController::class, 'getallusers']);

Route::post('/addcreator', [CreatorController::class, 'addcreator']);
Route::post('/getallcreator', [CreatorController::class, 'getallcreator']);
Route::post('/updatecreator', [CreatorController::class, 'updatecreator']);
Route::post('/deletecreator', [CreatorController::class, 'deletecreator']);

Route::post('/addclient', [ClientController::class, 'addclient']);
Route::post('/getallclient', [ClientController::class, 'getallclient']);
Route::post('/updateclient', [ClientController::class, 'updateclient']);
Route::post('/deleteclient', [ClientController::class, 'deleteclient']);

Route::post('/addsalesperson', [SalepersonController::class, 'addsalesperson']);
Route::post('/updatesalesperson', [SalepersonController::class, 'updatesalesperson']);
Route::post('/getallsalesperson', [SalepersonController::class, 'getallsalesperson']);
Route::post('/deletesalesperson', [SalepersonController::class, 'deletesalesperson']);

Route::post('/addsalesmanager', [SalesmanagerController::class, 'addsalesmanager']);
Route::post('/getallsalesmanager', [SalesmanagerController::class, 'getallsalesmanager']);
Route::post('/updatesalesmanager', [SalesmanagerController::class, 'updatesalesmanager']);
Route::post('/deletesalesmanager', [SalesmanagerController::class, 'deletesalesmanager']);

Route::post('/addsales', [SalesController::class, 'addsales']);
Route::post('/getallsales', [SalesController::class, 'getallsales']);
Route::post('/updatesales', [SalesController::class, 'updatesales']);
Route::post('/deletesales', [SalesController::class, 'deletesales']);
