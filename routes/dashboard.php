<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\OptionController;
use App\Http\Controllers\Dashboard\RequestController;
use App\Http\Controllers\Dashboard\StructureController;
use App\Http\Controllers\Dashboard\StructureDescController;
use App\Http\Controllers\Dashboard\UserController;
use App\Http\Controllers\Dashboard\WellController;
use App\Models\Structure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Route;


Route::group([
    'middleware'=>'auth:sanctum',config('jetstream.auth_session'),'verified'],function(){
        Route::get('/',[DashboardController::class,'index'])
            ->name('dashboard');
        Route::resource('/users',UserController::class);


        Route::post('/update-structure-order/{structure}', function(Request $request,Structure $structure){
            $structure->update(['order' => $request->newPosition]);
        })->name('update-structure-order');

        Route::resource('/optionStructures',OptionController::class)->only(['index','create','store'])
            ->middleware('check.super');
        Route::get('/optionStructures/delete/{id}',[OptionController::class,'deleteOption'])
            ->name('optionStructures.delete');

        Route::resource('/structures',StructureController::class)->only(['create','store']);
        Route::get('/structuresDesc/delete/{id}',[StructureController::class,'deleteStruct'])
            ->name('structures.delete');

        Route::resource('/structuresDesc',StructureDescController::class)->only(['edit','update']);
        Route::get('/structureDescription/delete/{id}',[StructureDescController::class,'deleteStructDesc'])
            ->name('deleteStructDesc');

        Route::resource('/wells',WellController::class);
        Route::get('wells/generatePDF/{id}',[WellController::class,'generatePDF'])
            ->name('wells.generatePDF');

        Route::resource('/requests',RequestController::class);
        Route::post('/requests/reject/{id}',[RequestController::class,'reject'])
            ->name('requests.reject');
});
