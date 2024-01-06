<?php

use App\Http\Controllers\Dashboard\DashboardController;
use App\Http\Controllers\Dashboard\OptionController;
use App\Http\Controllers\Dashboard\RequestController;
use App\Http\Controllers\Dashboard\StructureController;
use App\Http\Controllers\Dashboard\StructureDescController;
use App\Http\Controllers\Dashboard\SurveyController;
use App\Http\Controllers\Dashboard\SurveyStructureController;
use App\Http\Controllers\Dashboard\SurveyWellController;
use App\Http\Controllers\Dashboard\TestController;
use App\Http\Controllers\Dashboard\TestStructureController;
use App\Http\Controllers\Dashboard\TestWellController;
use App\Http\Controllers\Dashboard\TroubleshootController;
use App\Http\Controllers\Dashboard\TroubleshootStructureController;
use App\Http\Controllers\Dashboard\SurveyRequestController;
use App\Http\Controllers\Dashboard\SurveyStructureDescController;
use App\Http\Controllers\Dashboard\TestRequestController;
use App\Http\Controllers\Dashboard\TestStructureDescController;
use App\Http\Controllers\Dashboard\TroubleshootRequestController;
use App\Http\Controllers\Dashboard\TroubleshootStructureDescController;
use App\Http\Controllers\Dashboard\TroubleshootWellController;
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


        //Routes for (Option, Survey, Test, Troubleshoot)
        Route::resource('/optionStructures',OptionController::class)->only(['index','create','store'])
            ->middleware('check.super');
        Route::get('/optionStructures/delete/{id}',[OptionController::class,'deleteOption'])
            ->name('optionStructures.delete');

        Route::resource('/surveys',SurveyController::class)->only(['index','create','store'])
            ->middleware('check.super');
        Route::get('/survey/delete/{id}',[SurveyController::class,'deleteSurvey'])
            ->name('survey.delete');

        Route::resource('/tests',TestController::class)->only(['index','create','store'])
            ->middleware('check.super');
        Route::get('/test/delete/{id}',[TestController::class,'deleteTest'])
            ->name('test.delete');

        Route::resource('/troubleshoots',TroubleshootController::class)->only(['index','create','store'])
            ->middleware('check.super');
        Route::get('/troubleshoot/delete/{id}',[TroubleshootController::class,'deleteTroubleshoot'])
            ->name('troubleshoot.delete');


        //Routes for (OptionStructure, SurveyStructure, TestStructure, TroubleshootStructure) and (OptionStructureDesc, SurveyStructureDesc, TestStructureDesc, TroubleshootStructureDesc)
        Route::resource('/structures',StructureController::class)->only(['create','store']);
        Route::get('/structuresDesc/delete/{id}',[StructureController::class,'deleteStruct'])
            ->name('structures.delete');

        Route::resource('/surveystructures',SurveyStructureController::class)->only(['create','store']);
        Route::get('/surveyStructuresDesc/delete/{id}',[SurveyStructureController::class,'deleteStruct'])
            ->name('surveyStructure.delete');

        Route::resource('/teststructures',TestStructureController::class)->only(['create','store']);
        Route::get('/testStructuresDesc/delete/{id}',[TestStructureController::class,'deleteStruct'])
            ->name('testStructure.delete');

        Route::resource('/troubleshootstructures',TroubleshootStructureController::class)->only(['create','store']);
        Route::get('/troubleshootStructuresDesc/delete/{id}',[TroubleshootStructureController::class,'deleteStruct'])
            ->name('troubleshootStructure.delete');

        Route::resource('/structuresDesc',StructureDescController::class)->only(['edit','update']);
        Route::get('/structureDescription/delete/{id}',[StructureDescController::class,'deleteStructDesc'])
            ->name('deleteStructDesc');

        Route::resource('/surveystructuresdesc',SurveyStructureDescController::class)->only(['edit','update']);
        Route::get('/surveystructureDescription/delete/{id}',[SurveyStructureDescController::class,'deleteStructDesc'])
            ->name('surveydesc.delete');

        Route::resource('/teststructuresdesc',TestStructureDescController::class)->only(['edit','update']);
        Route::get('/teststructureDescription/delete/{id}',[TestStructureDescController::class,'deleteStructDesc'])
            ->name('testdesc.delete');

        Route::resource('/troubleshootstructuresdesc',TroubleshootStructureDescController::class)->only(['edit','update']);
        Route::get('/troubleshootstructureDescription/delete/{id}',[TroubleshootStructureDescController::class,'deleteStructDesc'])
            ->name('troubleshootdesc.delete');


        //Routes for (Well, SurveyWell, TestWell, TroubleshootWell) & Their PDFs
        Route::resource('/wells',WellController::class);
        Route::get('wells/generatePDF/{id}',[WellController::class,'generatePDF'])
            ->name('wells.generatePDF');

        Route::resource('/surveywells',SurveyWellController::class);
        Route::get('surveywells/generatePDF/{id}',[SurveyWellController::class,'generatePDF'])
            ->name('surveywells.generatePDF');

        Route::resource('/testwells',TestWellController::class);
        Route::get('testwells/generatePDF/{id}',[TestWellController::class,'generatePDF'])
            ->name('testwells.generatePDF');

        Route::resource('/troubleshootwells',TroubleshootWellController::class);
        Route::get('troubleshootwells/generatePDF/{id}',[TroubleshootWellController::class,'generatePDF'])
            ->name('troubleshootwells.generatePDF');


        //Routes for (Request, SurveyRequest, TestRequest, TroubleshootRequest)
        Route::resource('/requests',RequestController::class);
        Route::post('/requests/reject/{id}',[RequestController::class,'reject'])
            ->name('requests.reject');

        Route::resource('/surveyrequests',SurveyRequestController::class);
        Route::post('/survey/requests/reject/{id}',[SurveyRequestController::class,'reject'])
            ->name('surveyrequests.reject');

        Route::resource('/testrequests',TestRequestController::class);
        Route::post('/test/requests/reject/{id}',[TestRequestController::class,'reject'])
            ->name('testrequests.reject');

        Route::resource('/troubleshootrequests',TroubleshootRequestController::class);
        Route::post('/troubleshoot/requests/reject/{id}',[TroubleshootRequestController::class,'reject'])
            ->name('troubleshootrequests.reject');
});
