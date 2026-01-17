<?php

use App\Http\Controllers\Api\kasirController;
use App\Http\Controllers\Api\barangController;
use App\Http\Controllers\Api\detailJualController;
use App\Http\Controllers\Api\jualController;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;

Route::apiResource('kasirs', kasirController::class);
Route::apiResource('barangs', barangController::class);
Route::apiResource('juals', jualController::class);
Route::apiResource('detailJuals', detailJualController::class);