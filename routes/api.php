<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\NetSuiteController;
use App\Http\Controllers\NetSuiteAIController;

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

// Direct SuiteQL query execution
Route::post('/netsuite/suiteql', [NetSuiteController::class, 'executeSuiteQL']);

// AI Agent endpoints
Route::post('/netsuite/ai/ask', [NetSuiteAIController::class, 'ask']);
Route::get('/netsuite/ai/schema', [NetSuiteAIController::class, 'getSchema']);
Route::get('/netsuite/ai/tables', [NetSuiteAIController::class, 'listTables']);
Route::get('/netsuite/ai/status', [NetSuiteAIController::class, 'checkStatus']);

