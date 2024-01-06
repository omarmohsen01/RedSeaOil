<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\Front\SurveyWellDataServiceInterface;
use App\Models\SurveyRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Throwable;


class SurveyWellDataController extends Controller
{
    public $surveyWellDataService;
    public function __construct(SurveyWellDataServiceInterface $surveyWellDataService)
    {
        $this->surveyWellDataService=$surveyWellDataService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }

    public function store(Request $request)
    {
        try{
            if(isset($request->survey_well_id)){
                $this->surveyWellDataService->publishOldSurveyWell($request,'published');
                return response()->json(['message' => 'Survey Well Created Successfully'], 200);
            }else{
                $this->surveyWellDataService->publishNewSurveyWell($request,'published');
                return response()->json(['message' => 'Survey Well Created Successfully'], 200);
            }
        }catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

    public function show(string $id)
    {
        // $well=$this->well->where('id',$id)->where('published','as_draft')->first();
        // if(!$well){
        //     return response()->json(['message'=> 'this well is already published'],404);
        // }else{
        //     $well_data=$this->well_data->where('well_id',$well->id)
        //         ->with(['well','Structure_description'])->get();
        //         if(!$well_data){
        //             return response()->json(['message' => 'Well Not Found'], 404);
        //         }
        //         return response()->json($well_data,200);
        // }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $wellRequest=SurveyRequest::with('survey_well.well_data')->where('id',$id)->where('status','accept')->first();
        if($wellRequest)
        {
            $this->surveyWellDataService->requestToEdit($request,'published',$wellRequest);
            $wellRequest->delete();
            return response()->json(['message' => 'Survey Well Updated Successfully'], 200);
        }else{
            return response()->json(['message' => 'Request Have been Rejected,Or Something Wrong Happened'], 200);
        }

    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        //
    }
    public function saveDraft(Request $request)
    {
        try{
            if(isset($request->survey_well_id)){
                $this->surveyWellDataService->publishOldSurveyWell($request,'as_draft');
                return response()->json(['message' => 'Well Created Successfully'], 200);
            }else{
                $this->surveyWellDataService->publishNewSurveyWell($request,'as_draft');
                return response()->json(['message' => 'Well Created Successfully'], 200);
            }
        }catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
