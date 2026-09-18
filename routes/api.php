<?php

use App\Http\Controllers\MvelExecutionController;
use Illuminate\Support\Facades\Route;

Route::post('/mvel/execute', [MvelExecutionController::class, 'execute']);
Route::get('/mvel/health', [MvelExecutionController::class, 'health']);