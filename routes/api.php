<?php

use App\Http\Controllers\Api\AccessTokenController;
use App\Http\Controllers\Front\OptionController;
use App\Http\Controllers\Front\RequestController;
use App\Http\Controllers\Front\WellController;
use App\Http\Controllers\Front\WellDataController;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Route;

Route::post('auth/access-tokens',[AccessTokenController::class,'store'])
    ->middleware('guest:sanctum');

Route::group(['middleware'=>'auth:sanctum'],function(){
    Route::get('/user',function (){
        return Auth::guard('sanctum')->user();
    });

    Route::delete('auth/access-tokens/{token?}',[AccessTokenController::class,'destroy']);

    Route::get('options',[OptionController::class,'index']);

    Route::post('wellsData/saveDraft',[WellDataController::class,'saveDraft']);

    Route::apiResource('wellsData',WellDataController::class);

    Route::get('wells/userWells',[WellController::class,'userWell']);
    Route::get('wells/generatePDF/{id}',[WellController::class,'generatePDF']);

    Route::apiResource('wells',WellController::class);

    Route::apiResource('requests',RequestController::class);
});
