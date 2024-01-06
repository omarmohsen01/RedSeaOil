<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\Front\TestWellDataServiceInterface;
use App\Models\TestRequest;
use App\Models\TestStructure;
use App\Models\TestStructure_description;
use App\Models\TestWell;
use App\Models\TestWell_data;
use App\Models\Well;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Throwable;

class TestWellDataController extends Controller
{
    public $testWellDataService;
    public function __construct(TestWellDataServiceInterface $testWellDataService)
    {
        $this->testWellDataService=$testWellDataService;
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
            if(isset($request->test_well_id)){
                $this->testWellDataService->publishOldTestWell($request,'published');
                return response()->json(['message' => 'Test Well Created Successfully'], 200);
            }else{
                $this->testWellDataService->publishNewTestWell($request,'published');
                return response()->json(['message' => 'Test Well Created Successfully'], 200);
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
        $wellRequest=TestRequest::with('test_well.well_data')->where('id',$id)->where('status','accept')->first();
        if($wellRequest)
        {
            $this->testWellDataService->requestToEdit($request,'published',$wellRequest);
            $wellRequest->delete();
            return response()->json(['message' => 'Well Updated Successfully'], 200);
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
            if(isset($request->test_well_id)){
                $this->testWellDataService->publishOldTestWell($request,'as_draft');
                return response()->json(['message' => 'Test Well Created Successfully'], 200);
            }else{
                $this->testWellDataService->publishNewTestWell($request,'as_draft');
                return response()->json(['message' => 'Test Well Created Successfully'], 200);
            }
        }catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }

}
