<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\Front\TestWellDataServiceInterface;
use App\Models\TestRequest;
use App\Http\Controllers\Interfaces\Front\WellDataServiceInterface;
use App\Http\Requests\PublishTestWellRequest;
use App\Http\Requests\PublishWellRequest;
use App\Models\Request as ModelsRequest;
use App\Models\Structure;
use App\Models\Structure_description;
use App\Models\TestStructure;
use App\Models\TestStructure_description;
use App\Models\TestWell;
use App\Models\TestWell_data;
use App\Models\Well;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use App\Models\Well_data;

use Throwable;

class TestWellDataController extends Controller
{
    public $well,$well_data,$structure,$structure_description,$wellDataService;
    public function __construct(TestWell $well,TestWell_data $well_data,TestStructure $structure,TestStructure_description $structure_description,TestWellDataServiceInterface $wellDataService)
    {
        $this->well=$well;
        $this->well_data=$well_data;
        $this->wellDataService=$wellDataService;
        $this->structure=$structure;
        $this->structure_description=$structure_description;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {

    }


    public function store(PublishTestWellRequest $request)
    {
        try{
            if(isset($request->well_id)){
                $this->wellDataService->publishOldWell($request,'published');
                return response()->json(['message' => 'Well Created Successfully'], 200);
            }else{
                $this->wellDataService->publishNewWell($request,'published');
                return response()->json(['message' => 'Well Created Successfully'], 200);
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
        $well=$this->well->where('id',$id)->where('published','as_draft')->first();
        if(!$well){
            return response()->json(['message'=> 'this well is already published'],404);
        }else{
            $well_data=$this->well_data->where('well_id',$well->id)
                ->with(['well','Structure_description'])->get();
                if(!$well_data){
                    return response()->json(['message' => 'Well Not Found'], 404);
                }
                return response()->json($well_data,200);
        }
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $wellRequest=TestRequest::with('test_well.well_data')->where('id',$id)->where('status','accept')->first();
        if($wellRequest)
        {
            $this->wellDataService->requestToEdit($request,'published',$wellRequest);
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
                $this->wellDataService->publishOldTestWell($request,'as_draft');
                return response()->json(['message' => 'Test Well Created Successfully'], 200);
            }else{
                $this->wellDataService->publishNewTestWell($request,'as_draft');
                return response()->json(['message' => 'Test Well Created Successfully'], 200);
            }
        }catch (Throwable $e) {
            DB::rollBack();
            throw $e;
        }
    }


}
