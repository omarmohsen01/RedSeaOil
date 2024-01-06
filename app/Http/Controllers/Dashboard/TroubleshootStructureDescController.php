<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Http\Controllers\Interfaces\Dashboard\StructureDescServiceInterface;
use App\Http\Controllers\Interfaces\Dashboard\TroubleshootStructureDescServiceInterface;
use App\Http\Controllers\Services\Dashboard\StructureService;
use App\Http\Controllers\Services\Dashboard\TroubleshootStructureService;
use App\Http\Requests\StructureDescRequest;
use App\Models\Option;
use App\Models\Structure;
use App\Models\Structure_description;
use App\Models\TroubleshootStructure;
use App\Models\TroubleshootStructure_description;
use Illuminate\Http\Request;

class TroubleshootStructureDescController extends Controller
{
    public $structureDescService;
    public $structureService;
    public function __construct(TroubleshootStructureDescServiceInterface $structureDescService,TroubleshootStructureService $structureService)
    {
        $this->structureDescService=$structureDescService;
        $this->structureService=$structureService;
    }
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        abort(404);
    }

    /**
     * Show the form for creating a new resource.
     */
    public function create(Request $request)
    {

    }

    /**
     * Store a newly created resource in storage.
     */
    public function store(StructureDescRequest $request)
    {

    }

    /**
     * Display the specified resource.
     */
    public function show(string $id)
    {
        abort(404);
    }

    /**
     * Show the form for editing the specified resource.
     */
    public function edit(string $id)
    {
        $structure = TroubleshootStructure::with(['troubleshoot_struct_desc.user','troubleshoot'])->findOrFail($id);
        return view('dashboard.structureDesc.edit',compact('structure'));
    }

    /**
     * Update the specified resource in storage.
     */
    public function update(Request $request, string $id)
    {
        $this->structureDescService->structDescUpdate($request,$id);
        return redirect()->route('troubleshoots.index')
            ->with('success','Structure Updated Successfully');
    }

    /**
     * Remove the specified resource from storage.
     */
    public function destroy(string $id)
    {
        abort(404);
    }


    public function deleteStructDesc(string $id)
    {
        $structureDesc=TroubleshootStructure_description::find($id);
        return view('dashboard.structureDesc.delete',compact('structureDesc'));
    }
}
